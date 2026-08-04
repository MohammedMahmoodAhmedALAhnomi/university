import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../core/constants/api_endpoints.dart';
import 'storage_service.dart';

class ApiService {
  static String get _baseUrl {
    if (kIsWeb) {
      return ApiEndpoints.webBaseUrl;
    }
    return ApiEndpoints.baseUrl;
  }

  static Future<Map<String, String>> _getHeaders() async {
    final token = await StorageService.getToken();
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    if (token != null && token.isNotEmpty) {
      headers['Authorization'] = 'Bearer $token';
    }
    return headers;
  }

  static Future<dynamic> get(String endpoint, {Map<String, String>? queryParams}) async {
    try {
      Uri uri = Uri.parse('$_baseUrl$endpoint');
      if (queryParams != null && queryParams.isNotEmpty) {
        uri = uri.replace(queryParameters: queryParams);
      }
      final headers = await _getHeaders();
      final response = await http.get(uri, headers: headers).timeout(const Duration(seconds: 15));
      return _processResponse(response);
    } catch (e) {
      throw Exception('تعذر الاتصال بالسيرفر: $e');
    }
  }

  static Future<dynamic> post(String endpoint, Map<String, dynamic> body) async {
    try {
      final uri = Uri.parse('$_baseUrl$endpoint');
      final headers = await _getHeaders();
      final response = await http
          .post(uri, headers: headers, body: jsonEncode(body))
          .timeout(const Duration(seconds: 15));
      return _processResponse(response);
    } catch (e) {
      throw Exception('حدث خطأ أثناء الطلب: $e');
    }
  }

  static dynamic _processResponse(http.Response response) {
    final decoded = jsonDecode(utf8.decode(response.bodyBytes));
    if (response.statusCode >= 200 && response.statusCode < 300) {
      return decoded;
    } else {
      final msg = decoded['message'] ?? 'حدث خطأ في الخادم (${response.statusCode})';
      throw Exception(msg);
    }
  }
}
