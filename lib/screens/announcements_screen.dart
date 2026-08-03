import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/university_provider.dart';
import '../widgets/announcement_card.dart';

class AnnouncementsScreen extends StatefulWidget {
  const AnnouncementsScreen({super.key});

  @override
  State<AnnouncementsScreen> createState() => _AnnouncementsScreenState();
}

class _AnnouncementsScreenState extends State<AnnouncementsScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<UniversityProvider>(context, listen: false).fetchAnnouncements();
    });
  }

  @override
  Widget build(BuildContext context) {
    final uniProvider = Provider.of<UniversityProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('الإعلانات والأخبار'),
      ),
      body: uniProvider.isAnnouncementsLoading
          ? const Center(child: CircularProgressIndicator())
          : uniProvider.announcements.isEmpty
              ? const Center(child: Text('لا توجد إعلانات حالياً'))
              : ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: uniProvider.announcements.length,
                  itemBuilder: (context, index) {
                    return AnnouncementCard(announcement: uniProvider.announcements[index]);
                  },
                ),
    );
  }
}
