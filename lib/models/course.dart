class CourseModel {
  final int id;
  final int majorId;
  final int levelId;
  final int semesterId;
  final String name;
  final String? description;
  final String? majorName;
  final String? levelName;
  final String? semesterName;
  final double avgRating;
  final int ratingCount;
  final int filesCount;

  CourseModel({
    required this.id,
    required this.majorId,
    required this.levelId,
    required this.semesterId,
    required this.name,
    this.description,
    this.majorName,
    this.levelName,
    this.semesterName,
    required this.avgRating,
    required this.ratingCount,
    required this.filesCount,
  });

  factory CourseModel.fromJson(Map<String, dynamic> json) {
    return CourseModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      majorId: json['major_id'] is int ? json['major_id'] : int.parse(json['major_id'].toString()),
      levelId: json['level_id'] is int ? json['level_id'] : int.parse(json['level_id'].toString()),
      semesterId: json['semester_id'] is int ? json['semester_id'] : int.parse(json['semester_id'].toString()),
      name: json['name'] ?? '',
      description: json['description'],
      majorName: json['major_name'],
      levelName: json['level_name'],
      semesterName: json['semester_name'],
      avgRating: (json['avg_rating'] ?? 0).toDouble(),
      ratingCount: json['rating_count'] is int ? json['rating_count'] : int.parse((json['rating_count'] ?? 0).toString()),
      filesCount: json['files_count'] is int ? json['files_count'] : int.parse((json['files_count'] ?? 0).toString()),
    );
  }
}
