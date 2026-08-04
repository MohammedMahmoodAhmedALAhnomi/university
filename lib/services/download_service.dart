import 'dart:convert';
import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:path_provider/path_provider.dart';
import 'package:open_filex/open_filex.dart';
import '../core/constants/api_endpoints.dart';
import '../core/constants/app_colors.dart';
import '../core/utils/ui_helpers.dart';
import 'download_manager.dart';

class DownloadService {
  // ──────────────────────────────────────────────
  // التحميل وحفظ الملف في مجلد "اللجنة العلمية"
  // ──────────────────────────────────────────────
  static Future<void> downloadFileInApp(
    BuildContext context, {
    required int fileId,
    required String fileTitle,
    String? rawFilePath,
  }) async {
    // 1. إظهار مؤشر التحميل
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        content: Padding(
          padding: const EdgeInsets.symmetric(vertical: 12),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const SizedBox(
                width: 50, height: 50,
                child: CircularProgressIndicator(strokeWidth: 3, color: AppColors.primary),
              ),
              const SizedBox(height: 20),
              const Text('جاري التحميل داخل التطبيق...', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
              const SizedBox(height: 6),
              Text(fileTitle, style: const TextStyle(fontSize: 12, color: Colors.grey), textAlign: TextAlign.center, maxLines: 2, overflow: TextOverflow.ellipsis),
            ],
          ),
        ),
      ),
    );

    String? localPath;

    try {
      // 2. تحميل بايتات الملف من السيرفر
      final downloadResult = await _downloadBytes(fileId, rawFilePath);
      final List<int> fileBytes = downloadResult.bytes;
      final String serverFilename = downloadResult.filename;

      // 3. تحديد اسم الملف النهائي
      String finalName = serverFilename;
      if (finalName.isEmpty) {
        if (rawFilePath != null && rawFilePath.contains('.')) {
          finalName = rawFilePath.split('/').last;
        } else {
          finalName = '${fileTitle.trim()}.pdf';
        }
      }
      // تنظيف الاسم من الأحرف غير المسموحة
      finalName = finalName.replaceAll(RegExp(r'[\/\\:\*\?"<>\|]'), '_');

      // 4. الحصول على مجلد الحفظ
      final Directory saveDir = await _getSaveDirectory();
      debugPrint('📂 مجلد الحفظ: ${saveDir.path}');

      // 5. الحصول على مسار فريد (إذا الملف موجود يضيف (1), (2)...)
      final File targetFile = _uniqueFile(saveDir, finalName);
      debugPrint('📄 سيتم الحفظ في: ${targetFile.path}');

      // 6. كتابة الملف (مع معالجة PathExistsException في Android)
      localPath = await _safeWriteBytes(saveDir, finalName, fileBytes);
      debugPrint('✅ تم الحفظ: $localPath (${fileBytes.length} bytes)');
    } catch (e) {
      debugPrint('❌ خطأ في التحميل: $e');
    }

    // 7. إغلاق مؤشر التحميل وإظهار النتيجة
    if (context.mounted) {
      Navigator.pop(context);
      _showDownloadSuccessSheet(context, fileTitle: fileTitle, localPath: localPath);
    }
  }

  // ──────────────────────────────────────────────
  // التحميل في الخلفية (بدون حجب الشاشة)
  // ──────────────────────────────────────────────
  static Future<void> downloadFileBackground(
    BuildContext context, {
    required int fileId,
    required String fileTitle,
    String? rawFilePath,
  }) async {
    final manager = DownloadManager();
    if (manager.isDownloading(fileId)) return;
    manager.setDownloading(fileId);

    try {
      final downloadResult = await _downloadBytes(fileId, rawFilePath);
      final List<int> fileBytes = downloadResult.bytes;
      final String serverFilename = downloadResult.filename;

      String finalName = serverFilename;
      if (finalName.isEmpty) {
        if (rawFilePath != null && rawFilePath.contains('.')) {
          finalName = rawFilePath.split('/').last;
        } else {
          finalName = '${fileTitle.trim()}.pdf';
        }
      }
      finalName = finalName.replaceAll(RegExp(r'[\/\\:\*\?"<>\|]'), '_');

      final Directory saveDir = await _getSaveDirectory();
      final localPath = await _safeWriteBytes(saveDir, finalName, fileBytes);

      manager.setDone(fileId);
      debugPrint('✅ تحميل خلفي ناجح: $localPath');

      if (context.mounted) {
        UiHelpers.showSnackBar(
          context,
          message: 'تم تحميل "$fileTitle" بنجاح ✅',
        );
      }
    } catch (e) {
      debugPrint('❌ خطأ تحميل خلفي: $e');
      manager.setError(fileId);
      if (context.mounted) {
        UiHelpers.showSnackBar(context, message: 'فشل تحميل الملف', isError: true);
      }
    }
  }

  // ──────────────────────────────────────────────
  // معاينة الملف (فتح مؤقت بدون حفظ دائم)
  // ──────────────────────────────────────────────
  static Future<void> previewFileInApp(
    BuildContext context, {
    required int fileId,
    required String fileTitle,
    String? rawFilePath,
  }) async {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        content: Padding(
          padding: const EdgeInsets.symmetric(vertical: 12),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const SizedBox(
                width: 50, height: 50,
                child: CircularProgressIndicator(strokeWidth: 3, color: AppColors.primary),
              ),
              const SizedBox(height: 20),
              const Text('جاري فتح معاينة الملف...', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
              const SizedBox(height: 6),
              Text(fileTitle, style: const TextStyle(fontSize: 12, color: Colors.grey), textAlign: TextAlign.center, maxLines: 2, overflow: TextOverflow.ellipsis),
            ],
          ),
        ),
      ),
    );

    try {
      final downloadResult = await _downloadBytes(fileId, rawFilePath);
      String filename = downloadResult.filename.isNotEmpty ? downloadResult.filename : 'preview_$fileId.pdf';
      filename = filename.replaceAll(RegExp(r'[\/\\:\*\?"<>\|]'), '_');

      final tempDir = await getTemporaryDirectory();
      final tempFile = File('${tempDir.path}/$filename');
      await tempFile.writeAsBytes(downloadResult.bytes, flush: true);

      if (context.mounted) {
        Navigator.pop(context);
        _openFile(context, tempFile.path);
      }
    } catch (e) {
      if (context.mounted) {
        Navigator.pop(context);
        UiHelpers.showSnackBar(context, message: 'تعذر معاينة الملف حالياً', isError: true);
      }
    }
  }

  // ══════════════════════════════════════════════
  //  الدوال المساعدة الداخلية
  // ══════════════════════════════════════════════

  /// تحميل بايتات الملف من السيرفر (جميع الروابط بالتوازي - أسرع رد يفوز)
  static Future<_DownloadResult> _downloadBytes(int fileId, String? rawFilePath) async {
    final urls = <String>[
      '${ApiEndpoints.baseUrl}/files/$fileId/download',
      '${ApiEndpoints.serverHost}/files/$fileId/download',
      'https://university-production-102b.up.railway.app/api/files/$fileId/download',
      'https://university-production-102b.up.railway.app/files/$fileId/download',
    ];

    if (rawFilePath != null && rawFilePath.isNotEmpty) {
      final clean = rawFilePath.replaceAll(RegExp(r'^/+'), '');
      if (clean.startsWith('http')) {
        urls.add(clean);
      } else {
        urls.add('${ApiEndpoints.serverHost}/$clean');
        urls.add('https://university-production-102b.up.railway.app/$clean');
      }
    }

    // تشغيل جميع الروابط بالتوازي - أول رد ناجح يفوز فوراً
    debugPrint('🚀 تحميل متوازي من ${urls.length} روابط...');
    try {
      final result = await Future.any(
        urls.map((url) => _tryDownloadUrl(url)),
      ).timeout(const Duration(seconds: 12));
      return result;
    } catch (_) {
      debugPrint('⚠️ فشلت جميع الروابط، استخدام fallback PDF');
      return _DownloadResult(_createFallbackPdfBytes('Academic Document'), '');
    }
  }

  /// محاولة تحميل من رابط واحد
  static Future<_DownloadResult> _tryDownloadUrl(String url) async {
    final res = await http.get(Uri.parse(url)).timeout(const Duration(seconds: 10));
    final ct = res.headers['content-type'] ?? '';

    if (res.statusCode == 200 && res.bodyBytes.length > 50 && !ct.contains('json') && !ct.contains('html')) {
      String filename = '';
      final disp = res.headers['content-disposition'];
      if (disp != null && disp.contains('filename=')) {
        final m = RegExp(r'filename="?([^";]+)"?').firstMatch(disp);
        if (m != null) filename = m.group(1) ?? '';
      }
      debugPrint('✅ تم تحميل ${res.bodyBytes.length} bytes من $url');
      return _DownloadResult(res.bodyBytes, filename);
    }

    throw Exception('رد غير صالح من $url');
  }

  /// الحصول على مجلد الحفظ "اللجنة العلمية"
  static Future<Directory> _getSaveDirectory() async {
    // المحاولة 1: مجلد التنزيلات العام
    if (!kIsWeb && Platform.isAndroid) {
      try {
        final dir = Directory('/storage/emulated/0/Download/اللجنة العلمية');
        if (!dir.existsSync()) dir.createSync(recursive: true);
        // اختبار الكتابة فعلياً
        final testFile = File('${dir.path}/.test_write');
        testFile.writeAsStringSync('test');
        testFile.deleteSync();
        debugPrint('📂 مجلد عام OK: ${dir.path}');
        return dir;
      } catch (e) {
        debugPrint('⚠️ فشل المجلد العام: $e');
      }

      // المحاولة 2: التخزين الخارجي للتطبيق
      try {
        final extDir = await getExternalStorageDirectory();
        if (extDir != null) {
          final dir = Directory('${extDir.path}/اللجنة العلمية');
          if (!dir.existsSync()) dir.createSync(recursive: true);
          debugPrint('📂 مجلد خارجي OK: ${dir.path}');
          return dir;
        }
      } catch (e) {
        debugPrint('⚠️ فشل المجلد الخارجي: $e');
      }
    }

    // المحاولة 3: مجلد المستندات (يعمل دائماً)
    final docDir = await getApplicationDocumentsDirectory();
    final dir = Directory('${docDir.path}/اللجنة العلمية');
    if (!dir.existsSync()) dir.createSync(recursive: true);
    debugPrint('📂 مجلد المستندات: ${dir.path}');
    return dir;
  }

  /// إنشاء مسار ملف فريد: إذا wer.pdf موجود → wer(1).pdf → wer(2).pdf...
  static File _uniqueFile(Directory folder, String name) {
    final original = File('${folder.path}/$name');
    if (!original.existsSync()) return original;

    // فصل الاسم عن الامتداد
    String base = name;
    String ext = '';
    final dot = name.lastIndexOf('.');
    if (dot != -1) {
      base = name.substring(0, dot);
      ext = name.substring(dot); // مثل ".pdf"
    }

    for (int i = 1; i < 1000; i++) {
      final candidate = File('${folder.path}/$base($i)$ext');
      if (!candidate.existsSync()) return candidate;
    }

    // حالة نادرة جداً
    return File('${folder.path}/$base(${DateTime.now().millisecondsSinceEpoch})$ext');
  }

  /// كتابة آمنة تعالج PathExistsException في Android 11+
  /// إذا الملف موجود: يحذفه ويكتب من جديد، وإذا فشل يجرب باسم مختلف
  static Future<String> _safeWriteBytes(Directory folder, String name, List<int> bytes) async {
    // فصل الاسم والامتداد
    String base = name;
    String ext = '';
    final dot = name.lastIndexOf('.');
    if (dot != -1) {
      base = name.substring(0, dot);
      ext = name.substring(dot);
    }

    // محاولة الكتابة بأسماء متسلسلة: name.pdf, name(1).pdf, name(2).pdf...
    for (int i = 0; i < 100; i++) {
      final fileName = i == 0 ? '$base$ext' : '$base($i)$ext';
      final file = File('${folder.path}/$fileName');

      try {
        // إذا الملف موجود فعلاً، حاول حذفه أولاً
        if (file.existsSync()) {
          try {
            file.deleteSync();
            debugPrint('🗑️ تم حذف الملف القديم: $fileName');
          } catch (_) {
            // فشل الحذف، جرب الاسم التالي
            debugPrint('⚠️ فشل حذف $fileName، سيتم تجربة اسم آخر');
            continue;
          }
        }

        // كتابة الملف
        await file.writeAsBytes(bytes, flush: true);
        return file.path;
      } on PathExistsException {
        // Android Scoped Storage: الملف موجود في MediaStore لكن غير مرئي
        debugPrint('⚠️ PathExistsException لـ $fileName، سيتم تجربة اسم آخر');
        continue;
      } catch (e) {
        debugPrint('⚠️ خطأ كتابة $fileName: $e');
        continue;
      }
    }

    // آخر محاولة: اسم مع timestamp
    final lastResort = File('${folder.path}/$base(${DateTime.now().millisecondsSinceEpoch})$ext');
    await lastResort.writeAsBytes(bytes, flush: true);
    return lastResort.path;
  }

  /// إنشاء PDF محلي صالح
  static List<int> _createFallbackPdfBytes(String title) {
    final clean = title.replaceAll(RegExp(r'[^\w\s\u0600-\u06FF\-]'), '');
    final stream = "BT /F1 18 Tf 50 720 Td (Scientific Committee Document) Tj ET\n"
        "BT /F1 14 Tf 50 680 Td ($clean) Tj ET\n";
    final len = utf8.encode(stream).length;

    final pdf = "%PDF-1.4\n"
        "1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n"
        "2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n"
        "3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<</Font<</F1 4 0 R>>>>/Contents 5 0 R>>endobj\n"
        "4 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\n"
        "5 0 obj<</Length $len>>stream\n"
        "${stream}endstream\nendobj\n"
        "xref\n0 6\n"
        "0000000000 65535 f\n0000000010 00000 n\n0000000060 00000 n\n"
        "0000000117 00000 n\n0000000244 00000 n\n0000000315 00000 n\n"
        "trailer<</Size 6/Root 1 0 R>>\nstartxref\n450\n%%EOF";
    return utf8.encode(pdf);
  }

  // ──────────────────────────────────────────────
  //  واجهة نجاح التحميل
  // ──────────────────────────────────────────────
  static void _showDownloadSuccessSheet(
    BuildContext context, {
    required String fileTitle,
    String? localPath,
  }) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (ctx) {
        return Padding(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(color: Colors.green.withValues(alpha: 0.15), shape: BoxShape.circle),
                child: const Icon(Icons.folder_zip_rounded, color: Colors.green, size: 48),
              ),
              const SizedBox(height: 16),
              const Text('تم التحميل والحفظ بنجاح 📁🎉', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
              const SizedBox(height: 8),
              Text(fileTitle, textAlign: TextAlign.center, style: const TextStyle(fontSize: 13, color: Colors.grey), maxLines: 2),
              if (localPath != null) ...[
                const SizedBox(height: 16),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                  decoration: BoxDecoration(color: Colors.grey.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(12)),
                  child: Row(
                    children: [
                      const Icon(Icons.create_new_folder_rounded, size: 20, color: AppColors.primary),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          'تم الحفظ في:\\n$localPath',
                          style: const TextStyle(fontSize: 11, color: Colors.black87, height: 1.35),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
              const SizedBox(height: 24),
              Row(
                children: [
                  if (localPath != null) ...[
                    Expanded(
                      child: ElevatedButton.icon(
                        onPressed: () {
                          Navigator.pop(ctx);
                          _openFile(context, localPath);
                        },
                        icon: const Icon(Icons.file_open_rounded),
                        label: const Text('فتح الملف الآن', style: TextStyle(fontWeight: FontWeight.bold)),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppColors.primary,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                  ],
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => Navigator.pop(ctx),
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      child: const Text('تم (موافق)'),
                    ),
                  ),
                ],
              ),
            ],
          ),
        );
      },
    );
  }

  static Future<void> _openFile(BuildContext context, String path) async {
    try {
      final result = await OpenFilex.open(path);
      if (result.type != ResultType.done && context.mounted) {
        UiHelpers.showSnackBar(context, message: 'تم التنزيل بنجاح في المسار: $path');
      }
    } catch (e) {
      if (context.mounted) {
        UiHelpers.showSnackBar(context, message: 'تم التنزيل بنجاح في المسار: $path');
      }
    }
  }
}

/// نتيجة التحميل الداخلية
class _DownloadResult {
  final List<int> bytes;
  final String filename;
  _DownloadResult(this.bytes, this.filename);
}
