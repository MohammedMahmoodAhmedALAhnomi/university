import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../core/constants/api_endpoints.dart';

class NotificationModel {
  final int id;
  final int? userId;
  final String title;
  final String message;
  final String type;
  final String link;
  final bool isRead;
  final String createdAt;

  NotificationModel({
    required this.id,
    this.userId,
    required this.title,
    required this.message,
    required this.type,
    required this.link,
    required this.isRead,
    required this.createdAt,
  });

  factory NotificationModel.fromJson(Map<String, dynamic> json) {
    return NotificationModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      userId: json['user_id'] != null ? (json['user_id'] is int ? json['user_id'] : int.tryParse(json['user_id'].toString())) : null,
      title: json['title'] ?? '',
      message: json['message'] ?? '',
      type: json['type'] ?? 'info',
      link: json['link'] ?? '',
      isRead: json['is_read'] == true || json['is_read'] == 1,
      createdAt: json['created_at'] ?? '',
    );
  }
}

class NotificationProvider extends ChangeNotifier {
  bool _isLoading = false;
  int _unreadCount = 0;
  List<NotificationModel> _notifications = [];

  bool get isLoading => _isLoading;
  int get unreadCount => _unreadCount;
  List<NotificationModel> get notifications => _notifications;

  Future<void> fetchNotifications(int? userId) async {
    _isLoading = true;
    notifyListeners();

    try {
      final queryParams = userId != null ? {'user_id': userId.toString()} : null;
      final res = await ApiService.get(ApiEndpoints.notifications, queryParams: queryParams);
      if (res['status'] == 'success' && res['data'] != null) {
        _unreadCount = res['data']['unread_count'] ?? 0;
        final list = res['data']['notifications'] as List? ?? [];
        _notifications = list.map((e) => NotificationModel.fromJson(e)).toList();
      }
    } catch (e) {
      debugPrint('Error fetching notifications: $e');
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<void> markAsRead(int notificationId, int? userId) async {
    try {
      final queryParams = userId != null ? {'user_id': userId.toString()} : null;
      await ApiService.post('${ApiEndpoints.markNotificationRead}$notificationId/read', queryParams ?? {});
      final index = _notifications.indexWhere((n) => n.id == notificationId);
      if (index != -1) {
        _notifications[index] = NotificationModel(
          id: _notifications[index].id,
          userId: _notifications[index].userId,
          title: _notifications[index].title,
          message: _notifications[index].message,
          type: _notifications[index].type,
          link: _notifications[index].link,
          isRead: true,
          createdAt: _notifications[index].createdAt,
        );
        if (_unreadCount > 0) _unreadCount--;
        notifyListeners();
      }
    } catch (e) {
      debugPrint('Error marking notification as read: $e');
    }
  }

  Future<void> markAllAsRead(int? userId) async {
    try {
      final Map<String, dynamic> body = userId != null ? {'user_id': userId} : {};
      await ApiService.post(ApiEndpoints.readAllNotifications, body);

      _unreadCount = 0;
      _notifications = _notifications.map((n) => NotificationModel(
        id: n.id,
        userId: n.userId,
        title: n.title,
        message: n.message,
        type: n.type,
        link: n.link,
        isRead: true,
        createdAt: n.createdAt,
      )).toList();
      notifyListeners();
    } catch (e) {
      debugPrint('Error marking all notifications as read: $e');
    }
  }
}
