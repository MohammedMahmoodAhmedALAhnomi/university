import 'package:flutter/material.dart';

class AppConstants {
  static const String appName = 'اللجنة العلمية';
  static const String appSubTitle = 'نظام إدارة المحتوى التعليمي للجامعات';

  // Default Base API URL (Can be changed in settings inside the app)
  // For Android Emulator: http://10.0.2.2/university
  // For Windows / Localhost test: http://localhost/university
  // For Web / Production: Live Server Domain URL (e.g., https://your-domain.com)
  static const String defaultBaseUrl = 'http://localhost/university';

  // Storage Keys
  static const String keyBaseUrl = 'base_url';
  static const String keyAuthToken = 'auth_token';
  static const String keyUserData = 'user_data';
  static const String keyDarkMode = 'dark_mode';

  // App Palette
  static const Color primaryColor = Color(0xFF0F52BA); // Royal Blue
  static const Color primaryDarkColor = Color(0xFF0A367C);
  static const Color accentColor = Color(0xFF00C853); // Emerald Accent
  static const Color goldAccent = Color(0xFFFFB300); // Gold Accent for ratings
  static const Color backgroundLight = Color(0xFFF8FAFC);
  static const Color cardLight = Colors.white;
  static const Color backgroundDark = Color(0xFF0F172A);
  static const Color cardDark = Color(0xFF1E293B);
}
