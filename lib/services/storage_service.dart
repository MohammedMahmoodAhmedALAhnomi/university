import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user.dart';
import '../utils/constants.dart';

class StorageService {
  static SharedPreferences? _prefs;

  static Future<void> init() async {
    _prefs = await SharedPreferences.getInstance();
  }

  static String getBaseUrl() {
    return _prefs?.getString(AppConstants.keyBaseUrl) ?? AppConstants.defaultBaseUrl;
  }

  static Future<bool> setBaseUrl(String url) async {
    // Remove trailing slash if present
    final cleanUrl = url.trim().endsWith('/') ? url.trim().substring(0, url.trim().length - 1) : url.trim();
    return await _prefs?.setString(AppConstants.keyBaseUrl, cleanUrl) ?? false;
  }

  static String? getToken() {
    return _prefs?.getString(AppConstants.keyAuthToken);
  }

  static Future<bool> saveToken(String token) async {
    return await _prefs?.setString(AppConstants.keyAuthToken, token) ?? false;
  }

  static UserModel? getUser() {
    final raw = _prefs?.getString(AppConstants.keyUserData);
    if (raw != null && raw.isNotEmpty) {
      try {
        return UserModel.fromJson(jsonDecode(raw));
      } catch (e) {
        return null;
      }
    }
    return null;
  }

  static Future<bool> saveUser(UserModel user) async {
    return await _prefs?.setString(AppConstants.keyUserData, jsonEncode(user.toJson())) ?? false;
  }

  static Future<void> clearAuth() async {
    await _prefs?.remove(AppConstants.keyAuthToken);
    await _prefs?.remove(AppConstants.keyUserData);
  }

  static bool isDarkMode() {
    return _prefs?.getBool(AppConstants.keyDarkMode) ?? false;
  }

  static Future<bool> setDarkMode(bool value) async {
    return await _prefs?.setBool(AppConstants.keyDarkMode, value) ?? false;
  }
}
