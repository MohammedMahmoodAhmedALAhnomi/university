class AcademicFileModel {
  final int id;
  final int courseId;
  final String title;
  final String? description;
  final String filePath;
  final String fileType; // lecture, summary, model, exam, other
  final int downloadCount;
  final String? courseName;
  final String? majorName;
  final String? createdAt;

  AcademicFileModel({
    required this.id,
    required this.courseId,
    required this.title,
    this.description,
    required this.filePath,
    required this.fileType,
    this.downloadCount = 0,
    this.courseName,
    this.majorName,
    this.createdAt,
  });

  factory AcademicFileModel.fromJson(Map<String, dynamic> json) {
    return AcademicFileModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      courseId: json['course_id'] is int ? json['course_id'] : int.parse(json['course_id'].toString()),
      title: json['title'] ?? '',
      description: json['description'],
      filePath: json['file_path'] ?? '',
      fileType: json['file_type'] ?? 'other',
      downloadCount: json['download_count'] != null ? (int.tryParse(json['download_count'].toString()) ?? 0) : 0,
      courseName: json['course_name'],
      majorName: json['major_name'],
      createdAt: json['created_at'],
    );
  }

  String get fileTypeArabic {
    switch (fileType) {
      case 'lecture':
        return 'محاضرة';
      case 'summary':
        return 'ملخص';
      case 'model':
        return 'نموذج إجابة';
      case 'exam':
        return 'اختبار / امتحان';
      default:
        return 'ملف عام';
    }
  }
}
