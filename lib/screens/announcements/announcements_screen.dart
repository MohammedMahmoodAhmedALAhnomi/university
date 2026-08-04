import 'package:flutter/material.dart';
import '../../models/announcement_model.dart';
import '../../services/api_service.dart';
import '../../core/constants/api_endpoints.dart';
import '../../core/utils/ui_helpers.dart';
import 'announcement_details_screen.dart';

class AnnouncementsScreen extends StatefulWidget {
  const AnnouncementsScreen({super.key});

  @override
  State<AnnouncementsScreen> createState() => _AnnouncementsScreenState();
}

class _AnnouncementsScreenState extends State<AnnouncementsScreen> {
  bool _isLoading = true;
  List<AnnouncementModel> _announcements = [];

  @override
  void initState() {
    super.initState();
    _fetchAnnouncements();
  }

  Future<void> _fetchAnnouncements() async {
    setState(() => _isLoading = true);
    try {
      final res = await ApiService.get(ApiEndpoints.announcements);
      if (res['status'] == 'success' && res['data'] != null) {
        _announcements = (res['data'] as List)
            .map((e) => AnnouncementModel.fromJson(e))
            .toList();
      }
    } catch (e) {
      debugPrint('Error fetching announcements: $e');
    }
    setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('الإعلانات والأنشطة'),
      ),
      body: _isLoading
          ? Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                children: [
                  UiHelpers.buildSkeletonLoader(height: 90, borderRadius: 16),
                  UiHelpers.buildSkeletonLoader(height: 90, borderRadius: 16),
                  UiHelpers.buildSkeletonLoader(height: 90, borderRadius: 16),
                ],
              ),
            )
          : _announcements.isEmpty
              ? RefreshIndicator(
                  onRefresh: _fetchAnnouncements,
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    child: SizedBox(
                      height: MediaQuery.of(context).size.height * 0.7,
                      child: UiHelpers.buildEmptyState(
                        icon: Icons.campaign_outlined,
                        title: 'لا توجد إعلانات حالياً',
                        subtitle: 'اسحب لأسفل لتحديث قائمة التنبيهات والإعلانات الأكاديمية',
                      ),
                    ),
                  ),
                )
              : RefreshIndicator(
                  onRefresh: _fetchAnnouncements,
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: _announcements.length,
                    itemBuilder: (context, index) {
                      final item = _announcements[index];
                      return Card(
                        margin: const EdgeInsets.only(bottom: 12),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        elevation: 2,
                        child: ListTile(
                          leading: Container(
                            padding: const EdgeInsets.all(10),
                            decoration: BoxDecoration(
                              color: (item.isPinned ? Colors.amber : Colors.blue).withValues(alpha: 0.12),
                              shape: BoxShape.circle,
                            ),
                            child: Icon(
                              item.isPinned ? Icons.push_pin_rounded : Icons.campaign_rounded,
                              color: item.isPinned ? Colors.amber[800] : Colors.blue,
                              size: 24,
                            ),
                          ),
                          title: Text(
                            item.title,
                            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
                          ),
                          subtitle: Text(
                            item.content,
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                            style: TextStyle(color: Colors.grey[600], fontSize: 12, height: 1.3),
                          ),
                          trailing: const Icon(Icons.arrow_forward_ios_rounded, size: 16),
                          onTap: () {
                            Navigator.of(context).push(
                              MaterialPageRoute(
                                builder: (_) => AnnouncementDetailsScreen(announcement: item),
                              ),
                            );
                          },
                        ),
                      );
                    },
                  ),
                ),
    );
  }
}
