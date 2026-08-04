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

  static Future<dynamic> uploadMultipart(
    String endpoint, {
    required Map<String, String> fields,
    String? filePath,
    List<int>? fileBytes,
    String? filename,
  }) async {
    try {
      final uri = Uri.parse('$_baseUrl$endpoint');
      final request = http.MultipartRequest('POST', uri);
      final token = await StorageService.getToken();
      if (token != null && token.isNotEmpty) {
        request.headers['Authorization'] = 'Bearer $token';
      }
      fields.forEach((key, value) {
        request.fields[key] = value;
      });

      if (filePath != null && filePath.isNotEmpty && !kIsWeb) {
        request.files.add(await http.MultipartFile.fromPath('file', filePath));
      } else if (fileBytes != null && filename != null) {
        request.files.add(http.MultipartFile.fromBytes('file', fileBytes, filename: filename));
      }

      final streamedResponse = await request.send().timeout(const Duration(seconds: 45));
      final response = await http.Response.fromStream(streamedResponse);
      return _processResponse(response);
    } catch (e) {
      throw Exception('تعذر رفع الملف: $e');
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
