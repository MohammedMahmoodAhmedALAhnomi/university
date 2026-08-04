import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/academic_provider.dart';
import '../../core/constants/app_colors.dart';
import '../../core/utils/ui_helpers.dart';
import 'major_details_screen.dart';

class MajorsScreen extends StatefulWidget {
  const MajorsScreen({super.key});

  @override
  State<MajorsScreen> createState() => _MajorsScreenState();
}

class _MajorsScreenState extends State<MajorsScreen> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() {
      if (!mounted) return;
      Provider.of<AcademicProvider>(context, listen: false).fetchMajors();
    });
  }

  @override
  Widget build(BuildContext context) {
    final academic = Provider.of<AcademicProvider>(context);

    if (academic.isLoadingMajors) {
      return Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            UiHelpers.buildSkeletonLoader(height: 80, borderRadius: 16),
            UiHelpers.buildSkeletonLoader(height: 80, borderRadius: 16),
            UiHelpers.buildSkeletonLoader(height: 80, borderRadius: 16),
          ],
        ),
      );
    }

    if (academic.majors.isEmpty) {
      return RefreshIndicator(
        onRefresh: () => academic.fetchMajors(),
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: SizedBox(
            height: MediaQuery.of(context).size.height * 0.7,
            child: UiHelpers.buildEmptyState(
              icon: Icons.school_outlined,
              title: 'لا توجد تخصصات حالياً',
              subtitle: 'اسحب لأسفل لإعادة تحميل التخصصات الأكاديمية المتاحة',
            ),
          ),
        ),
      );
    }

    return Scaffold(
      body: RefreshIndicator(
        onRefresh: () => academic.fetchMajors(),
        child: ListView.builder(
          padding: const EdgeInsets.all(16),
          itemCount: academic.majors.length,
          itemBuilder: (context, index) {
            final major = academic.majors[index];
            return Container(
              margin: const EdgeInsets.only(bottom: 12),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.04),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Card(
                elevation: 0,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16),
                  side: BorderSide(color: Colors.grey.withValues(alpha: 0.2)),
                ),
                child: InkWell(
                  onTap: () {
                    Navigator.of(context).push(
                      MaterialPageRoute(
                        builder: (_) => MajorDetailsScreen(
                          majorId: major.id,
                          majorName: major.name,
                        ),
                      ),
                    );
                  },
                  borderRadius: BorderRadius.circular(16),
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: AppColors.primary.withValues(alpha: 0.1),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: const Icon(Icons.school_rounded, color: AppColors.primary, size: 28),
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                major.name,
                                style: const TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 4),
                              Text(
                                '${major.coursesCount} مادة دراسية متاحة',
                                style: TextStyle(
                                  fontSize: 13,
                                  color: Colors.grey[600],
                                ),
                              ),
                            ],
                          ),
                        ),
                        const Icon(Icons.arrow_forward_ios_rounded, color: Colors.grey, size: 18),
                      ],
                    ),
                  ),
                ),
              ),
            );
          },
        ),
      ),
    );
  }
}
