import 'package:flutter/material.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';
import '../services/storage_service.dart';
import '../core/constants/api_endpoints.dart';

class AuthProvider extends ChangeNotifier {
  UserModel? _user;
  bool _isLoading = false;
  String? _errorMessage;

  UserModel? get user => _user;
  bool get isLoading => _isLoading;
  bool get isAuthenticated => _user != null;
  String? get errorMessage => _errorMessage;

  AuthProvider() {
    initUser();
  }

  Future<void> initUser() async {
    _user = await StorageService.getUser();
    notifyListeners();
    if (_user != null) {
      refreshProfile();
    }
  }

  Future<void> refreshProfile() async {
    if (_user == null) return;
    try {
      final res = await ApiService.get(ApiEndpoints.profile, queryParams: {'id': _user!.id.toString()});
      if (res['status'] == 'success' && res['data']?['user'] != null) {
        _user = UserModel.fromJson(res['data']['user']);
        await StorageService.saveUser(_user!);
        notifyListeners();
      }
    } catch (e) {
      debugPrint('Error refreshing profile: $e');
    }
  }


  Future<bool> login(String email, String password) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await ApiService.post(ApiEndpoints.login, {
        'email': email,
        'password': password,
      });

      if (res['status'] == 'success') {
        _user = UserModel.fromJson(res['user']);
        final token = res['token'] ?? '';
        await StorageService.saveUser(_user!);
        await StorageService.saveToken(token);
        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _errorMessage = res['message'] ?? 'فشل تسجيل الدخول';
      }
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    }

    _isLoading = false;
    notifyListeners();
    return false;
  }

  Future<bool> register({
    required String fullName,
    required String email,
    required String password,
    String? phone,
    int? majorId,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await ApiService.post(ApiEndpoints.register, {
        'full_name': fullName,
        'email': email,
        'password': password,
        'phone': phone ?? '',
        'major_id': majorId,
      });

      if (res['status'] == 'success') {
        _user = UserModel.fromJson(res['user']);
        final token = res['token'] ?? '';
        await StorageService.saveUser(_user!);
        await StorageService.saveToken(token);
        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _errorMessage = res['message'] ?? 'فشل إنشاء الحساب';
      }
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    }

    _isLoading = false;
    notifyListeners();
    return false;
  }

  Future<void> logout() async {
    _user = null;
    await StorageService.clearAll();
    notifyListeners();
  }

  Future<bool> updateProfile(String fullName, String phone, int? majorId, {String? password}) async {
    if (_user == null) return false;
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await ApiService.post(ApiEndpoints.updateProfile, {
        'user_id': _user!.id,
        'full_name': fullName,
        'phone': phone,
        'major_id': majorId,
        if (password != null && password.isNotEmpty) 'password': password,
      });

      if (res['status'] == 'success' && res['data']?['user'] != null) {
        _user = UserModel.fromJson(res['data']['user']);
        await StorageService.saveUser(_user!);
        _isLoading = false;
        notifyListeners();
        return true;
      }
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    }

    _isLoading = false;
    notifyListeners();
    return false;
  }
}
