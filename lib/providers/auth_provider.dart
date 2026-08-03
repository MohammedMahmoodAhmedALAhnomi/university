import 'package:flutter/material.dart';
import '../models/user.dart';
import '../services/api_service.dart';
import '../services/storage_service.dart';

class AuthProvider extends ChangeNotifier {
  UserModel? _user;
  bool _isLoading = false;
  String? _errorMessage;

  UserModel? get user => _user;
  bool get isAuthenticated => _user != null;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  AuthProvider() {
    _user = StorageService.getUser();
  }

  Future<bool> login(String email, String password) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await ApiService.post('/login', {
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
      _errorMessage = e.toString();
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
      final res = await ApiService.post('/register', {
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
      _errorMessage = e.toString();
    }

    _isLoading = false;
    notifyListeners();
    return false;
  }

  Future<void> logout() async {
    _user = null;
    await StorageService.clearAuth();
    notifyListeners();
  }
}
