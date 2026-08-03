import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/university_provider.dart';
import '../widgets/major_card.dart';

class MajorsScreen extends StatelessWidget {
  const MajorsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final uniProvider = Provider.of<UniversityProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('التخصصات الأكاديمية'),
      ),
      body: uniProvider.majors.isEmpty
          ? const Center(child: Text('لا توجد تخصصات متوفرة'))
          : GridView.builder(
              padding: const EdgeInsets.all(16),
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                childAspectRatio: 1.15,
                crossAxisSpacing: 12,
                mainAxisSpacing: 12,
              ),
              itemCount: uniProvider.majors.length,
              itemBuilder: (context, index) {
                return MajorCard(major: uniProvider.majors[index]);
              },
            ),
    );
  }
}
