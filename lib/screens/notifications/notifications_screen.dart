import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/notification_provider.dart';
import '../../providers/auth_provider.dart';
import '../../core/constants/app_colors.dart';
import '../../core/utils/ui_helpers.dart';

class NotificationsScreen extends StatefulWidget {
  const NotificationsScreen({super.key});

  @override
  State<NotificationsScreen> createState() => _NotificationsScreenState();
}

class _NotificationsScreenState extends State<NotificationsScreen> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() {
      if (!mounted) return;
      final auth = Provider.of<AuthProvider>(context, listen: false);
      final userId = auth.isAuthenticated ? auth.user!.id : null;
      Provider.of<NotificationProvider>(context, listen: false).fetchNotifications(userId);
    });
  }

  @override
  Widget build(BuildContext context) {
    final auth = Provider.of<AuthProvider>(context);
    final userId = auth.isAuthenticated ? auth.user!.id : null;
    final provider = Provider.of<NotificationProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('التنبيهات والإشعارات'),
        actions: [
          if (provider.notifications.isNotEmpty && provider.unreadCount > 0)
            TextButton.icon(
              onPressed: () {
                provider.markAllAsRead(userId);
                UiHelpers.showSnackBar(context, message: 'تم قراءة جميع التنبيهات');
              },
              icon: const Icon(Icons.done_all_rounded, size: 18),
              label: const Text('قراءة الكل', style: TextStyle(fontWeight: FontWeight.bold)),
            ),
        ],
      ),
      body: provider.isLoading
          ? Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                children: [
                  UiHelpers.buildSkeletonLoader(height: 85, borderRadius: 16),
                  UiHelpers.buildSkeletonLoader(height: 85, borderRadius: 16),
                  UiHelpers.buildSkeletonLoader(height: 85, borderRadius: 16),
                ],
              ),
            )
          : provider.notifications.isEmpty
              ? RefreshIndicator(
                  onRefresh: () => provider.fetchNotifications(userId),
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    child: SizedBox(
                      height: MediaQuery.of(context).size.height * 0.7,
                      child: UiHelpers.buildEmptyState(
                        icon: Icons.notifications_none_rounded,
                        title: 'لا توجد إشعارات حالياً',
                        subtitle: 'ستظهر هنا جميع التنبيهات الأكاديمية والرسائل الإدارية الخاصة بك',
                      ),
                    ),
                  ),
                )
              : RefreshIndicator(
                  onRefresh: () => provider.fetchNotifications(userId),
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: provider.notifications.length,
                    itemBuilder: (context, index) {
                      final item = provider.notifications[index];
                      return Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        child: Card(
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                            side: BorderSide(
                              color: item.isRead
                                  ? Colors.transparent
                                  : AppColors.primary.withValues(alpha: 0.3),
                              width: item.isRead ? 0 : 1.5,
                            ),
                          ),
                          elevation: item.isRead ? 1 : 3,
                          child: InkWell(
                            onTap: () {
                              if (!item.isRead) {
                                provider.markAsRead(item.id, userId);
                              }
                            },
                            borderRadius: BorderRadius.circular(16),
                            child: Padding(
                              padding: const EdgeInsets.all(16),
                              child: Row(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Container(
                                    padding: const EdgeInsets.all(10),
                                    decoration: BoxDecoration(
                                      color: _getTypeColor(item.type).withValues(alpha: 0.12),
                                      shape: BoxShape.circle,
                                    ),
                                    child: Icon(
                                      _getTypeIcon(item.type),
                                      color: _getTypeColor(item.type),
                                      size: 22,
                                    ),
                                  ),
                                  const SizedBox(width: 14),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Row(
                                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                          children: [
                                            Expanded(
                                              child: Text(
                                                item.title,
                                                style: TextStyle(
                                                  fontWeight: item.isRead ? FontWeight.normal : FontWeight.bold,
                                                  fontSize: 15,
                                                ),
                                              ),
                                            ),
                                            if (!item.isRead)
                                              Container(
                                                width: 8,
                                                height: 8,
                                                decoration: const BoxDecoration(
                                                  color: AppColors.primary,
                                                  shape: BoxShape.circle,
                                                ),
                                              ),
                                          ],
                                        ),
                                        const SizedBox(height: 6),
                                        Text(
                                          item.message,
                                          style: TextStyle(
                                            color: Colors.grey[600],
                                            fontSize: 13,
                                            height: 1.4,
                                          ),
                                        ),
                                        const SizedBox(height: 8),
                                        Text(
                                          item.createdAt,
                                          style: TextStyle(color: Colors.grey[400], fontSize: 11),
                                        ),
                                      ],
                                    ),
                                  ),
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

  Color _getTypeColor(String type) {
    switch (type) {
      case 'warning':
        return Colors.amber[800]!;
      case 'success':
        return Colors.green;
      case 'error':
        return Colors.redAccent;
      default:
        return AppColors.primary;
    }
  }

  IconData _getTypeIcon(String type) {
    switch (type) {
      case 'warning':
        return Icons.warning_amber_rounded;
      case 'success':
        return Icons.check_circle_outline_rounded;
      case 'error':
        return Icons.error_outline_rounded;
      default:
        return Icons.notifications_active_rounded;
    }
  }
}
