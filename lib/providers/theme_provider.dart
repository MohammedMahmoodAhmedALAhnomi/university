import 'package:flutter/material.dart';
import '../services/storage_service.dart';

class ThemeProvider extends ChangeNotifier {
  bool _isDark = false;
  bool get isDark => _isDark;

  ThemeProvider() {
    _loadTheme();
  }

  Future<void> _loadTheme() async {
    _isDark = await StorageService.isDarkMode();
    notifyListeners();
  }

  Future<void> toggleTheme() async {
    _isDark = !_isDark;
    await StorageService.saveThemeMode(_isDark);
    notifyListeners();
  }
}
