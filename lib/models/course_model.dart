class CourseModel {
  final int id;
  final String name;
  final String? code;
  final int majorId;
  final int levelId;
  final int semesterId;
  final String? levelName;
  final int? levelNumber;
  final String? semesterName;
  final int? semesterNumber;
  final double avgRating;
  final int ratingCount;
  final int filesCount;

  CourseModel({
    required this.id,
    required this.name,
    this.code,
    required this.majorId,
    required this.levelId,
    required this.semesterId,
    this.levelName,
    this.levelNumber,
    this.semesterName,
    this.semesterNumber,
    this.avgRating = 0.0,
    this.ratingCount = 0,
    this.filesCount = 0,
  });

  factory CourseModel.fromJson(Map<String, dynamic> json) {
    return CourseModel(
      id: json['id'] != null ? (int.tryParse(json['id'].toString()) ?? 0) : 0,
      name: json['name']?.toString() ?? '',
      code: json['code']?.toString(),
      majorId: json['major_id'] != null ? (int.tryParse(json['major_id'].toString()) ?? 0) : 0,
      levelId: json['level_id'] != null ? (int.tryParse(json['level_id'].toString()) ?? 1) : 1,
      semesterId: json['semester_id'] != null ? (int.tryParse(json['semester_id'].toString()) ?? 1) : 1,
      levelName: json['level_name']?.toString(),
      levelNumber: json['level_number'] != null ? int.tryParse(json['level_number'].toString()) : null,
      semesterName: json['semester_name']?.toString(),
      semesterNumber: json['semester_number'] != null ? int.tryParse(json['semester_number'].toString()) : null,
      avgRating: json['avg_rating'] != null ? (double.tryParse(json['avg_rating'].toString()) ?? 0.0) : 0.0,
      ratingCount: json['rating_count'] != null ? (int.tryParse(json['rating_count'].toString()) ?? 0) : 0,
      filesCount: json['files_count'] != null ? (int.tryParse(json['files_count'].toString()) ?? 0) : 0,
    );
  }

}
