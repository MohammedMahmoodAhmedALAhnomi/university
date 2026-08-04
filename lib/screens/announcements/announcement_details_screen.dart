import 'package:flutter/material.dart';
import '../../models/announcement_model.dart';

class AnnouncementDetailsScreen extends StatelessWidget {
  final AnnouncementModel announcement;

  const AnnouncementDetailsScreen({super.key, required this.announcement});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('تفاصيل الإعلان'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (announcement.isPinned)
              Container(
                margin: const EdgeInsets.only(bottom: 16),
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(
                  color: Colors.amber.withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: Colors.amber),
                ),
                child: const Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(Icons.push_pin, color: Colors.amber, size: 18),
                    SizedBox(width: 6),
                    Text('إعلان مثبت في الصفحة الرئيسية', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.amber)),
                  ],
                ),
              ),

            Text(
              announcement.title,
              style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),

            if (announcement.createdAt != null)
              Text(
                'تاريخ النشر: ${announcement.createdAt}',
                style: TextStyle(color: Colors.grey[600], fontSize: 13),
              ),
            const SizedBox(height: 20),

            const Divider(),
            const SizedBox(height: 16),

            Text(
              announcement.content,
              style: const TextStyle(fontSize: 16, height: 1.6),
            ),
          ],
        ),
      ),
    );
  }
}
