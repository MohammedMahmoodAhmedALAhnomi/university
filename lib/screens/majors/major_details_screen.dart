import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/academic_provider.dart';
import '../../core/constants/app_colors.dart';
import 'course_details_screen.dart';

class MajorDetailsScreen extends StatefulWidget {
  final int majorId;
  final String majorName;

  const MajorDetailsScreen({
    super.key,
    required this.majorId,
    required this.majorName,
  });

  @override
  State<MajorDetailsScreen> createState() => _MajorDetailsScreenState();
}

class _MajorDetailsScreenState extends State<MajorDetailsScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 4, vsync: this);
    Future.microtask(() {
      if (!mounted) return;
      Provider.of<AcademicProvider>(context, listen: false).fetchMajorDetails(widget.majorId);
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final academic = Provider.of<AcademicProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: Text(widget.majorName),
        bottom: TabBar(
          controller: _tabController,
          labelColor: AppColors.primary,
          indicatorColor: AppColors.primary,
          unselectedLabelColor: Colors.grey,
          tabs: const [
            Tab(text: 'المستوى 1'),
            Tab(text: 'المستوى 2'),
            Tab(text: 'المستوى 3'),
            Tab(text: 'المستوى 4'),
          ],
        ),
      ),
      body: academic.isLoadingMajors
          ? const Center(child: CircularProgressIndicator())
          : TabBarView(
              controller: _tabController,
              children: List.generate(4, (levelIndex) {
                final levelNum = levelIndex + 1;
                var levelCourses = academic.majorCourses
                    .where((c) => (c.levelNumber ?? c.levelId) == levelNum)
                    .toList();

                // If specific level filter yields no result but majorHasCourses, fallback on Level 1 to show all courses
                if (levelCourses.isEmpty && levelIndex == 0 && academic.majorCourses.isNotEmpty) {
                  levelCourses = academic.majorCourses;
                }

                if (levelCourses.isEmpty) {
                  return Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.folder_off_outlined, size: 64, color: Colors.grey[400]),
                        const SizedBox(height: 12),
                        Text(
                          'لا توجد مواد دراسية مسجلة للمستوى $levelNum حالياً',
                          style: TextStyle(color: Colors.grey[600]),
                        ),
                      ],
                    ),
                  );
                }


                return ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: levelCourses.length,
                  itemBuilder: (context, index) {
                    final course = levelCourses[index];
                    return Card(
                      margin: const EdgeInsets.only(bottom: 12),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      child: ListTile(
                        leading: CircleAvatar(
                          backgroundColor: AppColors.secondary.withValues(alpha: 0.1),
                          child: const Icon(Icons.book_rounded, color: AppColors.secondary),
                        ),
                        title: Text(course.name, style: const TextStyle(fontWeight: FontWeight.bold)),
                        subtitle: Row(
                          children: [
                            const Icon(Icons.star, color: Colors.amber, size: 16),
                            const SizedBox(width: 4),
                            Text('${course.avgRating} (${course.ratingCount} تقييم)'),
                            const SizedBox(width: 12),
                            const Icon(Icons.description, color: Colors.grey, size: 16),
                            const SizedBox(width: 4),
                            Text('${course.filesCount} ملفات'),
                          ],
                        ),
                        trailing: const Icon(Icons.arrow_forward_ios_rounded, size: 16),
                        onTap: () {
                          Navigator.of(context).push(
                            MaterialPageRoute(
                              builder: (_) => CourseDetailsScreen(
                                courseId: course.id,
                                courseName: course.name,
                              ),
                            ),
                          );
                        },
                      ),
                    );
                  },
                );
              }),
            ),
    );
  }
}
