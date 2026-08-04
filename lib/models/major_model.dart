import 'package:flutter/material.dart';
import '../core/constants/app_colors.dart';

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

  /// Get Short Code Badge (CS, IT, IS, CYBER, AI, SE, GRAPH, DS)
  String get codeTag {
    if (code != null && code!.trim().isNotEmpty) {
      return code!.trim().toUpperCase();
    }
    final n = name.toLowerCase();
    if (n.contains('تقنية') || n.contains('تقنيه') || n.contains('it')) return 'IT';
    if (n.contains('نظم') || n.contains('is')) return 'IS';
    if (n.contains('علوم') || n.contains('حاسوب') || n.contains('cs')) return 'CS';
    if (n.contains('شبكات') || n.contains('net')) return 'NET';
    if (n.contains('أمن') || n.contains('امن') || n.contains('سيبراني') || n.contains('cyber')) return 'CYBER';
    if (n.contains('ذكاء') || n.contains('اصطناعي') || n.contains('ai')) return 'AI';
    if (n.contains('برمجيات') || n.contains('se')) return 'SE';
    if (n.contains('جراف') || n.contains('غراف') || n.contains('وسائط') || n.contains('graph')) return 'GRAPH';
    if (n.contains('بيانات') || n.contains('ds')) return 'DS';
    return 'MAJOR';
  }

  /// Get Major Icon (Matching Website FontAwesome mapping)
  IconData get iconData {
    final n = name.toLowerCase();
    if (n.contains('بيانات') || n.contains('data') || n.contains('ds')) {
      return Icons.storage_rounded;
    }
    if (n.contains('جراف') || n.contains('غراف') || n.contains('تصميم') || n.contains('وسائط') || n.contains('graph')) {
      return Icons.palette_rounded;
    }
    if (n.contains('نظم') || n.contains('is')) {
      return Icons.account_tree_rounded;
    }
    if (n.contains('تقنية') || n.contains('it')) {
      return Icons.terminal_rounded;
    }
    if (n.contains('علوم') || n.contains('cs') || n.contains('حاسوب')) {
      return Icons.code_rounded;
    }
    if (n.contains('أمن') || n.contains('سيبراني') || n.contains('cyber')) {
      return Icons.shield_rounded;
    }
    if (n.contains('برمجيات') || n.contains('se') || n.contains('هندسة')) {
      return Icons.developer_mode_rounded;
    }
    if (n.contains('ذكاء') || n.contains('ai')) {
      return Icons.psychology_rounded;
    }
    return Icons.school_rounded;
  }

  /// Get Theme Color for Icon & Badge
  Color get themeColor {
    final tag = codeTag;
    switch (tag) {
      case 'CS':
        return const Color(0xFF4F46E5);
      case 'IT':
        return const Color(0xFF0284C7);
      case 'IS':
        return const Color(0xFF0D9488);
      case 'CYBER':
        return const Color(0xFFDC2626);
      case 'AI':
        return const Color(0xFF7C3AED);
      case 'SE':
        return const Color(0xFF2563EB);
      case 'GRAPH':
        return const Color(0xFFDB2777);
      case 'DS':
        return const Color(0xFFD97706);
      default:
        return AppColors.primary;
    }
  }
}
