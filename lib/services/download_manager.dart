import 'package:flutter/foundation.dart';

/// مدير التحميلات - يتتبع حالة كل ملف يتم تحميله
class DownloadManager extends ChangeNotifier {
  // fileId → حالة التحميل
  final Map<int, DownloadState> _downloads = {};

  static final DownloadManager _instance = DownloadManager._();
  factory DownloadManager() => _instance;
  DownloadManager._();

  DownloadState getState(int fileId) => _downloads[fileId] ?? DownloadState.idle;

  bool isDownloading(int fileId) => _downloads[fileId] == DownloadState.downloading;
  bool isDone(int fileId) => _downloads[fileId] == DownloadState.done;

  void setDownloading(int fileId) {
    _downloads[fileId] = DownloadState.downloading;
    notifyListeners();
  }

  void setDone(int fileId) {
    _downloads[fileId] = DownloadState.done;
    notifyListeners();
    // إعادة الحالة بعد 3 ثواني
    Future.delayed(const Duration(seconds: 3), () {
      if (_downloads[fileId] == DownloadState.done) {
        _downloads[fileId] = DownloadState.idle;
        notifyListeners();
      }
    });
  }

  void setError(int fileId) {
    _downloads[fileId] = DownloadState.error;
    notifyListeners();
    Future.delayed(const Duration(seconds: 3), () {
      if (_downloads[fileId] == DownloadState.error) {
        _downloads[fileId] = DownloadState.idle;
        notifyListeners();
      }
    });
  }
}

enum DownloadState { idle, downloading, done, error }
