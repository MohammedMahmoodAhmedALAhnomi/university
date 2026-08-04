import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../services/api_service.dart';
import '../../core/constants/api_endpoints.dart';
import '../../core/constants/app_colors.dart';
import '../../core/utils/ui_helpers.dart';
import '../../services/download_service.dart';
import '../majors/course_details_screen.dart';

class SearchScreen extends StatefulWidget {
  const SearchScreen({super.key});

  @override
  State<SearchScreen> createState() => _SearchScreenState();
}

class _SearchScreenState extends State<SearchScreen> {
  final _searchController = TextEditingController();
  bool _isSearching = false;
  List _courses = [];
  List _files = [];
  String _selectedFilter = 'all'; // 'all', 'courses', 'files'

  void _performSearch(String q) async {
    if (q.trim().isEmpty) return;
    setState(() => _isSearching = true);

    try {
      final res = await ApiService.get(ApiEndpoints.search, queryParams: {'q': q.trim()});
      if (res['status'] == 'success' && res['data'] != null) {
        _courses = res['data']['courses'] as List? ?? [];
        _files = res['data']['files'] as List? ?? [];
      }
    } catch (e) {
      debugPrint('Search error: $e');
    }

    if (!mounted) return;
    setState(() => _isSearching = false);
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final hasSearched = _searchController.text.trim().isNotEmpty;
    final filteredCourses = (_selectedFilter == 'files') ? [] : _courses;
    final filteredFiles = (_selectedFilter == 'courses') ? [] : _files;
    final isEmptyResults = !_isSearching && hasSearched && filteredCourses.isEmpty && filteredFiles.isEmpty;

    return Scaffold(
      appBar: AppBar(
        title: TextField(
          controller: _searchController,
          autofocus: true,
          textInputAction: TextInputAction.search,
          decoration: InputDecoration(
            hintText: 'ابحث عن مادة، ملخص، نموذج امتحان...',
            border: InputBorder.none,
            suffixIcon: _searchController.text.isNotEmpty
                ? IconButton(
                    icon: const Icon(Icons.clear_rounded),
                    onPressed: () {
                      _searchController.clear();
                      setState(() {
                        _courses = [];
                        _files = [];
                      });
                    },
                  )
                : null,
          ),
          onChanged: (val) {
            setState(() {});
          },
          onSubmitted: _performSearch,
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.search_rounded),
            onPressed: () => _performSearch(_searchController.text),
          ),
        ],
      ),
      body: Column(
        children: [
          // Filter Chips Bar
          if (hasSearched) ...[
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              color: Colors.grey.withValues(alpha: 0.05),
              child: Row(
                children: [
                  _buildFilterChip('الكل', 'all'),
                  const SizedBox(width: 8),
                  _buildFilterChip('المواد (${_courses.length})', 'courses'),
                  const SizedBox(width: 8),
                  _buildFilterChip('الملفات (${_files.length})', 'files'),
                ],
              ),
            ),
          ],

          Expanded(
            child: _isSearching
                ? Padding(
                    padding: const EdgeInsets.all(16.0),
                    child: Column(
                      children: [
                        UiHelpers.buildSkeletonLoader(height: 70, borderRadius: 14),
                        UiHelpers.buildSkeletonLoader(height: 70, borderRadius: 14),
                        UiHelpers.buildSkeletonLoader(height: 70, borderRadius: 14),
                      ],
                    ),
                  )
                : !hasSearched
                    ? UiHelpers.buildEmptyState(
                        icon: Icons.search_rounded,
                        title: 'ابحث في محتوى المنصة',
                        subtitle: 'ادخل اسم المادة الدراسية، أستاذ المادة، أو عنوان الملف للوصول السريع إليها',
                      )
                    : isEmptyResults
                        ? UiHelpers.buildEmptyState(
                            icon: Icons.search_off_rounded,
                            title: 'لم نجد نتائج مطابقة',
                            subtitle: 'تأكد من كتابة الكلمات بشكل صحيح أو حاول البحث بكتابة جزء من الاسم',
                          )
                        : SingleChildScrollView(
                            padding: const EdgeInsets.all(16),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                // Courses Results
                                if (filteredCourses.isNotEmpty) ...[
                                  Text(
                                    'المواد الدراسية (${filteredCourses.length}):',
                                    style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                                  ),
                                  const SizedBox(height: 8),
                                  ListView.builder(
                                    shrinkWrap: true,
                                    physics: const NeverScrollableScrollPhysics(),
                                    itemCount: filteredCourses.length,
                                    itemBuilder: (context, index) {
                                      final course = filteredCourses[index];
                                      return Card(
                                        margin: const EdgeInsets.only(bottom: 8),
                                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                                        child: ListTile(
                                          leading: CircleAvatar(
                                            backgroundColor: AppColors.primary.withValues(alpha: 0.1),
                                            child: const Icon(Icons.book_rounded, color: AppColors.primary),
                                          ),
                                          title: Text(course['name'] ?? '', style: const TextStyle(fontWeight: FontWeight.bold)),
                                          subtitle: Text(course['code'] ?? ''),
                                          trailing: const Icon(Icons.arrow_forward_ios_rounded, size: 16),
                                          onTap: () {
                                            Navigator.of(context).push(
                                              MaterialPageRoute(
                                                builder: (_) => CourseDetailsScreen(
                                                  courseId: course['id'],
                                                  courseName: course['name'],
                                                ),
                                              ),
                                            );
                                          },
                                        ),
                                      );
                                    },
                                  ),
                                  const SizedBox(height: 20),
                                ],

                                // Files Results
                                if (filteredFiles.isNotEmpty) ...[
                                  Text(
                                    'الملفات والملخصات (${filteredFiles.length}):',
                                    style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                                  ),
                                  const SizedBox(height: 8),
                                  ListView.builder(
                                    shrinkWrap: true,
                                    physics: const NeverScrollableScrollPhysics(),
                                    itemCount: filteredFiles.length,
                                    itemBuilder: (context, index) {
                                      final file = filteredFiles[index];
                                      return Card(
                                        margin: const EdgeInsets.only(bottom: 8),
                                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                                        child: ListTile(
                                          leading: Container(
                                            padding: const EdgeInsets.all(10),
                                            decoration: BoxDecoration(
                                              color: Colors.redAccent.withValues(alpha: 0.1),
                                              shape: BoxShape.circle,
                                            ),
                                            child: const Icon(Icons.picture_as_pdf, color: Colors.redAccent, size: 22),
                                          ),
                                          title: Text(file['title'] ?? '', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                                          trailing: Row(
                                            mainAxisSize: MainAxisSize.min,
                                            children: [
                                              IconButton(
                                                icon: const Icon(Icons.remove_red_eye_rounded, color: Colors.blueAccent),
                                                tooltip: 'معاينة الملف',
                                                onPressed: () {
                                                  DownloadService.previewFileInApp(
                                                    context,
                                                    fileId: file['id'] ?? 0,
                                                    fileTitle: file['title'] ?? '',
                                                    rawFilePath: file['file_path'],
                                                  );
                                                },
                                              ),
                                              IconButton(
                                                icon: const Icon(Icons.download_rounded, color: AppColors.primary),
                                                tooltip: 'تحميل الملف',
                                                onPressed: () {
                                                  DownloadService.downloadFileInApp(
                                                    context,
                                                    fileId: file['id'] ?? 0,
                                                    fileTitle: file['title'] ?? '',
                                                    rawFilePath: file['file_path'],
                                                  );
                                                },
                                              ),
                                            ],
                                          ),
                                        ),
                                      );
                                    },
                                  ),
                                ],
                              ],
                            ),
                          ),
          ),
        ],
      ),
    );
  }

  Widget _buildFilterChip(String label, String value) {
    final isSelected = _selectedFilter == value;
    return ChoiceChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (selected) {
        if (selected) {
          setState(() => _selectedFilter = value);
        }
      },
      selectedColor: AppColors.primary,
      labelStyle: TextStyle(
        color: isSelected ? Colors.white : Colors.grey[800],
        fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
        fontSize: 12,
      ),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
    );
  }
}
