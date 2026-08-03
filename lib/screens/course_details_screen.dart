import 'package:flutter/material.dart';
import 'package:flutter_rating_bar/flutter_rating_bar.dart';
import 'package:provider/provider.dart';
import '../models/file_item.dart';
import '../providers/university_provider.dart';
import '../utils/constants.dart';
import '../widgets/file_tile.dart';
import '../widgets/rating_dialog.dart';

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

class _CourseDetailsScreenState extends State<CourseDetailsScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 5, vsync: this);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<UniversityProvider>(context, listen: false)
          .fetchCourseDetails(widget.courseId);
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Widget _buildFilesTab(List<FileItemModel>? files) {
    if (files == null || files.isEmpty) {
      return const Center(
        child: Text('لا توجد ملفات في هذا القسم حالياً'),
      );
    }
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: files.length,
      itemBuilder: (context, index) {
        return FileTile(fileItem: files[index]);
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final uniProvider = Provider.of<UniversityProvider>(context);
    final course = uniProvider.currentCourse;
    final catFiles = uniProvider.categorizedFiles;

    return Scaffold(
      appBar: AppBar(
        title: Text(widget.courseName),
      ),
      body: uniProvider.isCourseLoading
          ? const Center(child: CircularProgressIndicator())
          : Column(
              children: [
                // Header Info Box
                Container(
                  padding: const EdgeInsets.all(16),
                  color: Theme.of(context).cardColor,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  course?.name ?? widget.courseName,
                                  style: const TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                if (course?.majorName != null)
                                  Text(
                                    '${course?.majorName} • ${course?.levelName} • ${course?.semesterName}',
                                    style: TextStyle(
                                      fontSize: 13,
                                      color: Colors.grey.shade600,
                                    ),
                                  ),
                              ],
                            ),
                          ),
                          ElevatedButton.icon(
                            style: ElevatedButton.styleFrom(
                              backgroundColor: AppConstants.goldAccent,
                              foregroundColor: Colors.black87,
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                            ),
                            icon: const Icon(Icons.star, size: 18),
                            label: const Text('تقييم المادة', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                            onPressed: () {
                              showDialog(
                                context: context,
                                builder: (_) => RatingDialog(
                                  courseId: widget.courseId,
                                  courseName: widget.courseName,
                                ),
                              );
                            },
                          ),
                        ],
                      ),
                      if (course?.description != null && course!.description!.isNotEmpty) ...[
                        const SizedBox(height: 10),
                        Text(
                          course.description!,
                          style: TextStyle(
                            fontSize: 13,
                            color: Theme.of(context).textTheme.bodyMedium?.color?.withOpacity(0.8),
                          ),
                        ),
                      ],
                      const SizedBox(height: 12),
                      Row(
                        children: [
                          RatingBarIndicator(
                            rating: course?.avgRating ?? 0,
                            itemBuilder: (context, index) => const Icon(
                              Icons.star,
                              color: AppConstants.goldAccent,
                            ),
                            itemCount: 5,
                            itemSize: 18.0,
                          ),
                          const SizedBox(width: 8),
                          Text(
                            '${course?.avgRating ?? 0} من 5  (${course?.ratingCount ?? 0} تقييمات)',
                            style: const TextStyle(
                              fontSize: 13,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),

                // Category Tabs Bar
                TabBar(
                  controller: _tabController,
                  isScrollable: true,
                  labelColor: AppConstants.primaryColor,
                  unselectedLabelColor: Colors.grey,
                  indicatorColor: AppConstants.primaryColor,
                  tabs: const [
                    Tab(text: 'الكل'),
                    Tab(text: 'المحاضرات'),
                    Tab(text: 'الملخصات'),
                    Tab(text: 'نماذج الاختبارات'),
                    Tab(text: 'أخرى'),
                  ],
                ),
                const Divider(height: 1),

                // Tab Views
                Expanded(
                  child: TabBarView(
                    controller: _tabController,
                    children: [
                      _buildFilesTab(uniProvider.courseFiles),
                      _buildFilesTab(catFiles['lecture']),
                      _buildFilesTab(catFiles['summary']),
                      _buildFilesTab([
                        ...(catFiles['model'] ?? []),
                        ...(catFiles['exam'] ?? []),
                      ]),
                      _buildFilesTab(catFiles['other']),
                    ],
                  ),
                ),
              ],
            ),
    );
  }
}
