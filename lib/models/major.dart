class MajorModel {
  final int id;
  final String name;
  final String? description;
  final String icon;
  final bool isActive;
  final int coursesCount;

  MajorModel({
    required this.id,
    required this.name,
    this.description,
    required this.icon,
    required this.isActive,
    required this.coursesCount,
  });

  factory MajorModel.fromJson(Map<String, dynamic> json) {
    return MajorModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      name: json['name'] ?? '',
      description: json['description'],
      icon: json['icon'] ?? 'fa-graduation-cap',
      isActive: json['is_active'].toString() == '1' || json['is_active'] == true,
      coursesCount: json['courses_count'] != null
          ? (json['courses_count'] is int
              ? json['courses_count']
              : int.parse(json['courses_count'].toString()))
          : 0,
    );
  }
}
