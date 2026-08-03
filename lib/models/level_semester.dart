class LevelModel {
  final int id;
  final String name;
  final int levelNumber;

  LevelModel({
    required this.id,
    required this.name,
    required this.levelNumber,
  });

  factory LevelModel.fromJson(Map<String, dynamic> json) {
    return LevelModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      name: json['name'] ?? '',
      levelNumber: json['level_number'] is int
          ? json['level_number']
          : int.parse(json['level_number'].toString()),
    );
  }
}

class SemesterModel {
  final int id;
  final String name;
  final int semesterNumber;

  SemesterModel({
    required this.id,
    required this.name,
    required this.semesterNumber,
  });

  factory SemesterModel.fromJson(Map<String, dynamic> json) {
    return SemesterModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      name: json['name'] ?? '',
      semesterNumber: json['semester_number'] is int
          ? json['semester_number']
          : int.parse(json['semester_number'].toString()),
    );
  }
}
