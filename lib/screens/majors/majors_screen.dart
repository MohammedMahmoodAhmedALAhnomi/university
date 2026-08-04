import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/academic_provider.dart';
import '../../core/constants/app_colors.dart';
import '../../core/utils/ui_helpers.dart';
import '../../widgets/major_card_widget.dart';
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
      appBar: AppBar(
        title: const Text('التخصصات الأكاديمية'),
      ),
      body: RefreshIndicator(
        onRefresh: () => academic.fetchMajors(),
        child: GridView.builder(
          padding: const EdgeInsets.all(16),
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            crossAxisSpacing: 14,
            mainAxisSpacing: 14,
            childAspectRatio: 0.92,
          ),
          itemCount: academic.majors.length,
          itemBuilder: (context, index) {
            final major = academic.majors[index];
            return MajorGridCardWidget(major: major);
          },
        ),
      ),
    );
  }
}
