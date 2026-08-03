class FileItemModel {
  final int id;
  final int courseId;
  final int uploadedBy;
  final String title;
  final String? description;
  final String fileName;
  final String filePath;
  final int fileSize;
  final String fileExtension;
  final String fileType; // lecture, summary, model, exam, other
  final int downloadCount;
  final String? courseName;
  final String? majorName;
  final String createdAt;

  FileItemModel({
    required this.id,
    required this.courseId,
    required this.uploadedBy,
    required this.title,
    this.description,
    required this.fileName,
    required this.filePath,
    required this.fileSize,
    required this.fileExtension,
    required this.fileType,
    required this.downloadCount,
    this.courseName,
    this.majorName,
    required this.createdAt,
  });

  factory FileItemModel.fromJson(Map<String, dynamic> json) {
    return FileItemModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      courseId: json['course_id'] is int ? json['course_id'] : int.parse(json['course_id'].toString()),
      uploadedBy: json['uploaded_by'] is int ? json['uploaded_by'] : int.parse(json['uploaded_by'].toString()),
      title: json['title'] ?? '',
      description: json['description'],
      fileName: json['file_name'] ?? '',
      filePath: json['file_path'] ?? '',
      fileSize: json['file_size'] is int ? json['file_size'] : int.parse((json['file_size'] ?? 0).toString()),
      fileExtension: json['file_extension'] ?? 'pdf',
      fileType: json['file_type'] ?? 'other',
      downloadCount: json['download_count'] is int ? json['download_count'] : int.parse((json['download_count'] ?? 0).toString()),
      courseName: json['course_name'],
      majorName: json['major_name'],
      createdAt: json['created_at'] ?? '',
    );
  }

  String get formattedSize {
    if (fileSize <= 0) return '0 B';
    if (fileSize < 1024) return '$fileSize B';
    if (fileSize < 1024 * 1024) return '${(fileSize / 1024).toStringAsFixed(1)} KB';
    return '${(fileSize / (1024 * 1024)).toStringAsFixed(1)} MB';
  }

  String get fileTypeName {
    switch (fileType) {
      case 'lecture':
        return 'محاضرة';
      case 'summary':
        return 'ملخص';
      case 'model':
        return 'نموذج إجابة';
      case 'exam':
        return 'نموذج اختبار';
      default:
        return 'ملف آخر';
    }
  }
}
