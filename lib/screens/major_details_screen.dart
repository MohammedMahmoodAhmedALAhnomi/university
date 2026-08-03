import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/university_provider.dart';
import '../widgets/course_card.dart';

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

class _MajorDetailsScreenState extends State<MajorDetailsScreen> {
  int? _selectedLevelId;
  int? _selectedSemesterId;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<UniversityProvider>(context, listen: false)
          .fetchMajorDetails(widget.majorId);
    });
  }

  @override
  Widget build(BuildContext context) {
    final uniProvider = Provider.of<UniversityProvider>(context);

    // Filter courses based on selected Level & Semester
    final filteredCourses = uniProvider.majorCourses.where((c) {
      if (_selectedLevelId != null && c.levelId != _selectedLevelId) {
        return false;
      }
      if (_selectedSemesterId != null && c.semesterId != _selectedSemesterId) {
        return false;
      }
      return true;
    }).toList();

    return Scaffold(
      appBar: AppBar(
        title: Text(widget.majorName),
      ),
      body: uniProvider.isMajorLoading
          ? const Center(child: CircularProgressIndicator())
          : Column(
              children: [
                // Filter Options Bar
                Container(
                  padding: const EdgeInsets.all(12),
                  color: Theme.of(context).cardColor,
                  child: Row(
                    children: [
                      // Level Filter
                      Expanded(
                        child: DropdownButtonFormField<int?>(
                          initialValue: _selectedLevelId,
                          isExpanded: true,
                          decoration: const InputDecoration(
                            labelText: 'المستوى',
                            contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                          ),
                          items: [
                            const DropdownMenuItem<int?>(
                              value: null,
                              child: Text('جميع المستويات'),
                            ),
                            ...uniProvider.levels.map((lvl) {
                              return DropdownMenuItem<int?>(
                                value: lvl.id,
                                child: Text(lvl.name),
                              );
                            }),
                          ],
                          onChanged: (val) {
                            setState(() {
                              _selectedLevelId = val;
                            });
                          },
                        ),
                      ),
                      const SizedBox(width: 8),
                      // Semester Filter
                      Expanded(
                        child: DropdownButtonFormField<int?>(
                          initialValue: _selectedSemesterId,
                          isExpanded: true,
                          decoration: const InputDecoration(
                            labelText: 'الفصل الدراسي',
                            contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                          ),
                          items: [
                            const DropdownMenuItem<int?>(
                              value: null,
                              child: Text('جميع الفصول'),
                            ),
                            ...uniProvider.semesters.map((sem) {
                              return DropdownMenuItem<int?>(
                                value: sem.id,
                                child: Text(sem.name),
                              );
                            }),
                          ],
                          onChanged: (val) {
                            setState(() {
                              _selectedSemesterId = val;
                            });
                          },
                        ),
                      ),
                    ],
                  ),
                ),
                const Divider(height: 1),

                // Courses List
                Expanded(
                  child: filteredCourses.isEmpty
                      ? const Center(
                          child: Text(
                            'لا توجد مواد دراسية مطابقة للفلترة الحالية',
                            style: TextStyle(fontSize: 15),
                          ),
                        )
                      : ListView.builder(
                          padding: const EdgeInsets.all(16),
                          itemCount: filteredCourses.length,
                          itemBuilder: (context, index) {
                            return CourseCard(course: filteredCourses[index]);
                          },
                        ),
                ),
              ],
            ),
    );
  }
}
