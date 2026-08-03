class AnnouncementModel {
  final int id;
  final String title;
  final String content;
  final String? imagePath;
  final bool isPinned;
  final String createdAt;

  AnnouncementModel({
    required this.id,
    required this.title,
    required this.content,
    this.imagePath,
    required this.isPinned,
    required this.createdAt,
  });

  factory AnnouncementModel.fromJson(Map<String, dynamic> json) {
    return AnnouncementModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      title: json['title'] ?? '',
      content: json['content'] ?? '',
      imagePath: json['image_path'],
      isPinned: json['is_pinned'].toString() == '1' || json['is_pinned'] == true,
      createdAt: json['created_at'] ?? '',
    );
  }
}
