class UserModel {
  final int id;
  final String fullName;
  final String email;
  final String phone;
  final String role;
  final int? majorId;
  final int? managedLevelId;
  final int? managedMajorId;

  UserModel({
    required this.id,
    required this.fullName,
    required this.email,
    required this.phone,
    required this.role,
    this.majorId,
    this.managedLevelId,
    this.managedMajorId,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      fullName: json['full_name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'] ?? '',
      role: json['role'] ?? 'guest',
      majorId: json['major_id'] != null ? int.tryParse(json['major_id'].toString()) : null,
      managedLevelId: json['managed_level_id'] != null ? int.tryParse(json['managed_level_id'].toString()) : null,
      managedMajorId: json['managed_major_id'] != null ? int.tryParse(json['managed_major_id'].toString()) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'full_name': fullName,
      'email': email,
      'phone': phone,
      'role': role,
      'major_id': majorId,
      'managed_level_id': managedLevelId,
      'managed_major_id': managedMajorId,
    };
  }

  bool get isAdmin => role == 'admin';
  bool get isManager => role == 'manager';
}
