import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:flutter_rating_bar/flutter_rating_bar.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../providers/academic_provider.dart';
import '../../providers/auth_provider.dart';
import '../../providers/bookmark_provider.dart';
import '../../core/constants/app_colors.dart';
import '../../core/constants/api_endpoints.dart';
import '../../core/utils/ui_helpers.dart';
import '../../services/download_service.dart';
import '../../widgets/download_button.dart';
import '../files/upload_file_screen.dart';


class CourseDetailsScreen extends StatefulWidget {
  final int courseId;
  final String courseName;

  const CourseDetailsScreen({
    super.key,
    required this.courseId,
    required this.courseName,
  });

  @override
  State<CourseDetailsScreen> createState() => _CourseDetailsScreenState();
}

class _CourseDetailsScreenState extends State<CourseDetailsScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;

  final List<Map<String, String>> _categories = const [
    {'key': 'all', 'label': 'الكل'},
    {'key': 'lecture', 'label': 'المحاضرات'},
    {'key': 'summary', 'label': 'الملخصات'},
    {'key': 'model', 'label': 'النماذج'},
    {'key': 'exam', 'label': 'الامتحانات'},
  ];

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: _categories.length, vsync: this);
    Future.microtask(() {
      if (!mounted) return;
      final academic = Provider.of<AcademicProvider>(context, listen: false);
      academic.fetchCourseDetails(widget.courseId);
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  void _showRatingDialog(BuildContext context) {
    double selectedRating = 5;
    final academic = Provider.of<AcademicProvider>(context, listen: false);

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text('تقييم المادة الدراسية', textAlign: TextAlign.center, style: TextStyle(fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text('كيف تقيم مفردات ومحتوى جودة هذه المادة؟'),
            const SizedBox(height: 16),
            RatingBar.builder(
              initialRating: 5,
              minRating: 1,
              direction: Axis.horizontal,
              allowHalfRating: false,
              itemCount: 5,
              itemPadding: const EdgeInsets.symmetric(horizontal: 4.0),
              itemBuilder: (context, _) => const Icon(Icons.star_rounded, color: Colors.amber),
              onRatingUpdate: (rating) {
                selectedRating = rating;
              },
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(),
            child: const Text('إلغاء'),
          ),
          ElevatedButton(
            onPressed: () async {
              Navigator.of(ctx).pop();
              final success = await academic.rateCourse(widget.courseId, selectedRating.toInt());
              if (context.mounted) {
                if (success) {
                  UiHelpers.showSnackBar(context, message: 'شكراً لك! تم تسليم تقييمك للمادة بنجاح');
                } else {
                  UiHelpers.showSnackBar(context, message: 'عذراً، فشل إرسال التقييم', isError: true);
                }
              }
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.primary,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
            ),
            child: const Text('إرسال التقييم'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final academic = Provider.of<AcademicProvider>(context);
    final course = academic.currentCourse;

    return Scaffold(
      appBar: AppBar(
        title: Text(widget.courseName),
        actions: [
          IconButton(
            icon: const Icon(Icons.star_rounded, color: Colors.amber),
            tooltip: 'تقييم المادة',
            onPressed: () => _showRatingDialog(context),
          ),
        ],
        bottom: TabBar(
          controller: _tabController,
          isScrollable: true,
          labelColor: AppColors.primary,
          unselectedLabelColor: Colors.grey[600],
          indicatorColor: AppColors.primary,
          indicatorWeight: 3,
          labelStyle: const TextStyle(fontWeight: FontWeight.bold),
          tabs: _categories.map((c) => Tab(text: c['label'])).toList(),
        ),
      ),
      body: academic.isLoadingCourseDetails
          ? Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                children: [
                  UiHelpers.buildSkeletonLoader(height: 70, borderRadius: 16),
                  const SizedBox(height: 16),
                  UiHelpers.buildSkeletonLoader(height: 80, borderRadius: 16),
                  UiHelpers.buildSkeletonLoader(height: 80, borderRadius: 16),
                ],
              ),
            )
          : Column(
              children: [
                // Course Info Card Header
                if (course != null)
                  Container(
                    padding: const EdgeInsets.all(16),
                    color: AppColors.primary.withValues(alpha: 0.06),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              course.name,
                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                            ),
                            const SizedBox(height: 4),
                            Text('الرمز: ${course.code ?? "غير محدد"}', style: TextStyle(fontSize: 12, color: Colors.grey[700])),
                          ],
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(
                            color: Colors.amber.withValues(alpha: 0.15),
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: Colors.amber.withValues(alpha: 0.3)),
                          ),
                          child: Row(
                            children: [
                              const Icon(Icons.star_rounded, color: Colors.amber, size: 20),
                              const SizedBox(width: 4),
                              Text(
                                '${course.avgRating}',
                                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
                              ),
                              Text(' (${course.ratingCount})', style: TextStyle(fontSize: 11, color: Colors.grey[700])),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),

                // Files List View TabBar
                Expanded(
                  child: TabBarView(
                    controller: _tabController,
                    children: _categories.map((cat) {
                      final key = cat['key']!;
                      final files = key == 'all'
                          ? academic.courseFiles
                          : (academic.categorizedFiles[key] ?? []);

                      if (files.isEmpty) {
                        return UiHelpers.buildEmptyState(
                          icon: Icons.insert_drive_file_outlined,
                          title: 'لا توجد ملفات حالياً',
                          subtitle: 'لم يتم إرفاق ملفات تعليمية تحت تصنيف "${cat['label']}" بعد',
                        );
                      }

                      return ListView.builder(
                        padding: const EdgeInsets.all(16),
                        itemCount: files.length,
                        itemBuilder: (context, index) {
                          final file = files[index];

                          return Card(
                            margin: const EdgeInsets.only(bottom: 12),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                            elevation: 2,
                            child: ListTile(
                              leading: Container(
                                padding: const EdgeInsets.all(10),
                                decoration: BoxDecoration(
                                  color: Colors.redAccent.withValues(alpha: 0.1),
                                  shape: BoxShape.circle,
                                ),
                                child: const Icon(Icons.picture_as_pdf, color: Colors.redAccent, size: 24),
                              ),
                              title: Text(file.title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                              subtitle: Text('${file.fileTypeArabic} • ${file.downloadCount} تنزيل', style: const TextStyle(fontSize: 12)),
                              trailing: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Consumer2<AuthProvider, BookmarkProvider>(
                                    builder: (context, auth, bookmarkProvider, _) {
                                      final isBookmarked = bookmarkProvider.isBookmarked(file.id);
                                      return IconButton(
                                        icon: Icon(
                                          isBookmarked ? Icons.favorite_rounded : Icons.favorite_border_rounded,
                                          color: isBookmarked ? Colors.red : Colors.grey,
                                        ),
                                        tooltip: isBookmarked ? 'إزالة من المفضلة' : 'حفظ في المفضلة',
                                        onPressed: () {
                                          if (!auth.isAuthenticated) {
                                            UiHelpers.showSnackBar(context, message: 'يرجى تسجيل الدخول لحفظ الملفات في المفضلة', isError: true);
                                            return;
                                          }
                                          bookmarkProvider.toggleBookmark(auth.user!.id, file.id);
                                          UiHelpers.showSnackBar(
                                            context,
                                            message: isBookmarked ? 'تم إزالة الملف من المفضلة' : 'تم حفظ الملف في المفضلة بنجاح',
                                          );
                                        },
                                      );
                                    },
                                  ),
                                    IconButton(
                                      icon: const Icon(Icons.remove_red_eye_rounded, color: Colors.blueAccent),
                                      tooltip: 'معاينة الملف',
                                      onPressed: () {
                                        DownloadService.previewFileInApp(
                                          context,
                                          fileId: file.id,
                                          fileTitle: file.title,
                                          rawFilePath: file.filePath,
                                        );
                                      },
                                    ),
                                    DownloadButton(
                                      fileId: file.id,
                                      fileTitle: file.title,
                                      rawFilePath: file.filePath,
                                    ),
                                ],
                              ),

                            ),
                          );
                        },
                      );
                    }).toList(),
                  ),
                ),
              ],
            ),
      floatingActionButton: Consumer<AuthProvider>(
        builder: (context, auth, _) {
          final isDelegateOrAdmin = auth.isDelegate || auth.isAdmin || auth.isMajorAdmin;
          if (!isDelegateOrAdmin) return const SizedBox.shrink();
          return FloatingActionButton.extended(
            backgroundColor: AppColors.primary,
            foregroundColor: Colors.white,
            icon: const Icon(Icons.cloud_upload_rounded),
            label: const Text('رفع ملف لهذه المادة', style: TextStyle(fontWeight: FontWeight.bold)),
            onPressed: () async {
              final result = await Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) => UploadFileScreen(
                    initialCourseId: widget.courseId,
                    initialCourseName: widget.courseName,
                  ),
                ),
              );
              if (result == true && context.mounted) {
                Provider.of<AcademicProvider>(context, listen: false).fetchCourseDetails(widget.courseId);
              }
            },
          );
        },
      ),
    );
  }
}
