class MajorModel {
  final int id;
  final String name;
  final String? code;
  final String? description;
  final bool isActive;
  final int coursesCount;

  MajorModel({
    required this.id,
    required this.name,
    this.code,
    this.description,
    this.isActive = true,
    this.coursesCount = 0,
  });

  factory MajorModel.fromJson(Map<String, dynamic> json) {
    return MajorModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      name: json['name'] ?? '',
      code: json['code'],
      description: json['description'],
      isActive: json['is_active'] == 1 || json['is_active'] == true || json['is_active'] == '1',
      coursesCount: json['courses_count'] != null
          ? (json['courses_count'] is int ? json['courses_count'] : int.tryParse(json['courses_count'].toString()) ?? 0)
          : 0,
    );
  }
}
