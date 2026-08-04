class AnnouncementModel {
  final int id;
  final String title;
  final String content;
  final bool isPinned;
  final bool isActive;
  final String? createdAt;

  AnnouncementModel({
    required this.id,
    required this.title,
    required this.content,
    this.isPinned = false,
    this.isActive = true,
    this.createdAt,
  });

  factory AnnouncementModel.fromJson(Map<String, dynamic> json) {
    return AnnouncementModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      title: json['title'] ?? '',
      content: json['content'] ?? '',
      isPinned: json['is_pinned'] == 1 || json['is_pinned'] == true || json['is_pinned'] == '1',
      isActive: json['is_active'] == 1 || json['is_active'] == true || json['is_active'] == '1',
      createdAt: json['created_at'],
    );
  }
}
