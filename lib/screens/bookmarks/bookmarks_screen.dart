import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../providers/bookmark_provider.dart';
import '../../providers/auth_provider.dart';
import '../../core/constants/app_colors.dart';
import '../../core/constants/api_endpoints.dart';
import '../../core/utils/ui_helpers.dart';
import '../../services/download_service.dart';
import '../auth/login_screen.dart';

class BookmarksScreen extends StatefulWidget {
  const BookmarksScreen({super.key});

  @override
  State<BookmarksScreen> createState() => _BookmarksScreenState();
}

class _BookmarksScreenState extends State<BookmarksScreen> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() {
      if (!mounted) return;
      final auth = Provider.of<AuthProvider>(context, listen: false);
      if (auth.isAuthenticated) {
        Provider.of<BookmarkProvider>(context, listen: false).fetchBookmarks(auth.user!.id);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final auth = Provider.of<AuthProvider>(context);
    final bookmarkProvider = Provider.of<BookmarkProvider>(context);

    if (!auth.isAuthenticated) {
      return Scaffold(
        appBar: AppBar(title: const Text('المفضلة والمحفوظات')),
        body: Center(
          child: Padding(
            padding: const EdgeInsets.all(24.0),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.bookmark_border_rounded, size: 80, color: AppColors.primary),
                const SizedBox(height: 16),
                const Text(
                  'سجل الدخول لعرض المحفوظات',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 8),
                Text(
                  'يمكنك حفظ المحاضرات والملخصات الهامة في المفضلة للرجوع إليها بسرعة في أي وقت',
                  textAlign: TextAlign.center,
                  style: TextStyle(color: Colors.grey[600], fontSize: 13),
                ),
                const SizedBox(height: 24),
                ElevatedButton.icon(
                  onPressed: () {
                    Navigator.of(context).push(
                      MaterialPageRoute(builder: (_) => const LoginScreen()),
                    );
                  },
                  icon: const Icon(Icons.login_rounded),
                  label: const Text('تسجيل الدخول الآن'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                ),
              ],
            ),
          ),
        ),
      );
    }

    final userId = auth.user!.id;

    return Scaffold(
      appBar: AppBar(
        title: const Text('المفضلة والمحفوظات'),
      ),
      body: bookmarkProvider.isLoading
          ? Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                children: [
                  UiHelpers.buildSkeletonLoader(height: 80, borderRadius: 16),
                  UiHelpers.buildSkeletonLoader(height: 80, borderRadius: 16),
                  UiHelpers.buildSkeletonLoader(height: 80, borderRadius: 16),
                ],
              ),
            )
          : bookmarkProvider.bookmarks.isEmpty
              ? RefreshIndicator(
                  onRefresh: () => bookmarkProvider.fetchBookmarks(userId),
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    child: SizedBox(
                      height: MediaQuery.of(context).size.height * 0.7,
                      child: UiHelpers.buildEmptyState(
                        icon: Icons.bookmark_border_rounded,
                        title: 'لا توجد ملفات محفوظة',
                        subtitle: 'احفظ الملخصات والامتحانات والمراجع بالضغط على أيقونة المفضلة لرجوع سريع إليها',
                      ),
                    ),
                  ),
                )
              : RefreshIndicator(
                  onRefresh: () => bookmarkProvider.fetchBookmarks(userId),
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: bookmarkProvider.bookmarks.length,
                    itemBuilder: (context, index) {
                      final file = bookmarkProvider.bookmarks[index];

                      return Card(
                        margin: const EdgeInsets.only(bottom: 12),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        elevation: 2,
                        child: ListTile(
                          contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                          leading: Container(
                            padding: const EdgeInsets.all(10),
                            decoration: BoxDecoration(
                              color: AppColors.primary.withValues(alpha: 0.1),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: const Icon(Icons.picture_as_pdf_rounded, color: AppColors.primary, size: 26),
                          ),
                          title: Text(
                            file.title,
                            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                          ),
                          subtitle: Text(
                            '${file.courseName ?? ''} • ${file.fileTypeArabic}',
                            style: const TextStyle(fontSize: 12),
                          ),
                          trailing: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              IconButton(
                                icon: const Icon(Icons.favorite_rounded, color: Colors.red),
                                tooltip: 'إزالة من المفضلة',
                                onPressed: () {
                                  bookmarkProvider.toggleBookmark(userId, file.id);
                                  UiHelpers.showSnackBar(context, message: 'تم إزالة الملف من المفضلة');
                                },
                              ),
                              IconButton(
                                icon: const Icon(Icons.download_rounded, color: AppColors.primary),
                                tooltip: 'تحميل الملف داخل التطبيق',
                                onPressed: () {
                                  DownloadService.downloadFileInApp(
                                    context,
                                    fileId: file.id,
                                    fileTitle: file.title,
                                    rawFilePath: file.filePath,
                                  );
                                },
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),
    );
  }
}
