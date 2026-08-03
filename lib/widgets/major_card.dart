import 'package:flutter/material.dart';
import '../models/major.dart';
import '../screens/major_details_screen.dart';
import '../utils/constants.dart';

class MajorCard extends StatelessWidget {
  final MajorModel major;

  const MajorCard({super.key, required this.major});

  IconData _getIconData(String iconName) {
    switch (iconName) {
      case 'fa-laptop-code':
      case 'fa-code':
        return Icons.code;
      case 'fa-calculator':
        return Icons.calculate;
      case 'fa-microchip':
        return Icons.memory;
      case 'fa-briefcase':
        return Icons.business_center;
      case 'fa-book-open':
        return Icons.menu_book;
      default:
        return Icons.school;
    }
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    return Card(
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (_) => MajorDetailsScreen(majorId: major.id, majorName: major.name),
            ),
          );
        },
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: AppConstants.primaryColor.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  _getIconData(major.icon),
                  size: 32,
                  color: AppConstants.primaryColor,
                ),
              ),
              const SizedBox(height: 12),
              Text(
                major.name,
                textAlign: TextAlign.center,
                style: const TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: 16,
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
              const SizedBox(height: 6),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: theme.colorScheme.primary.withOpacity(0.08),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text(
                  '${major.coursesCount} مادة دراسية',
                  style: TextStyle(
                    fontSize: 12,
                    color: theme.colorScheme.primary,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
