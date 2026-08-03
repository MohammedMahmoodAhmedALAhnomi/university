import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/university_provider.dart';
import '../widgets/course_card.dart';
import '../widgets/file_tile.dart';

class SearchScreen extends StatefulWidget {
  const SearchScreen({super.key});

  @override
  State<SearchScreen> createState() => _SearchScreenState();
}

class _SearchScreenState extends State<SearchScreen> {
  final _searchController = TextEditingController();

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _onSearchSubmitted(String query) {
    if (query.trim().isNotEmpty) {
      Provider.of<UniversityProvider>(context, listen: false).search(query.trim());
    }
  }

  @override
  Widget build(BuildContext context) {
    final uniProvider = Provider.of<UniversityProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: TextField(
          controller: _searchController,
          autofocus: true,
          style: const TextStyle(color: Colors.white),
          decoration: const InputDecoration(
            hintText: 'ابحث عن مادة، محاضرة أو ملف...',
            hintStyle: TextStyle(color: Colors.white70),
            border: InputBorder.none,
            enabledBorder: InputBorder.none,
            focusedBorder: InputBorder.none,
          ),
          onSubmitted: _onSearchSubmitted,
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.search),
            onPressed: () => _onSearchSubmitted(_searchController.text),
          ),
        ],
      ),
      body: uniProvider.isSearchLoading
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (uniProvider.searchResultsCourses.isNotEmpty) ...[
                    const Text(
                      'المواد الدراسية المطابقة',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 8),
                    ...uniProvider.searchResultsCourses.map((c) => CourseCard(course: c)),
                    const SizedBox(height: 24),
                  ],
                  if (uniProvider.searchResultsFiles.isNotEmpty) ...[
                    const Text(
                      'الملفات والمحاضرات المطابقة',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 8),
                    ...uniProvider.searchResultsFiles.map((f) => FileTile(fileItem: f)),
                    const SizedBox(height: 24),
                  ],
                  if (!uniProvider.isSearchLoading &&
                      _searchController.text.isNotEmpty &&
                      uniProvider.searchResultsCourses.isEmpty &&
                      uniProvider.searchResultsFiles.isEmpty)
                    const Center(
                      child: Padding(
                        padding: EdgeInsets.symmetric(vertical: 40),
                        child: Text(
                          'لم يتم العثور على نتائج مطابقة للبحث',
                          style: TextStyle(fontSize: 16),
                        ),
                      ),
                    ),
                  if (_searchController.text.isEmpty)
                    const Center(
                      child: Padding(
                        padding: EdgeInsets.symmetric(vertical: 40),
                        child: Column(
                          children: [
                            Icon(Icons.search, size: 64, color: Colors.grey),
                            SizedBox(height: 12),
                            Text(
                              'اكتب كلمة البحث واضغط على زر البحث في الأعلَى',
                              style: TextStyle(fontSize: 14, color: Colors.grey),
                            ),
                          ],
                        ),
                      ),
                    ),
                ],
              ),
            ),
    );
  }
}
