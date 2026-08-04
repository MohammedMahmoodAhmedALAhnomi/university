import 'package:flutter/material.dart';
import '../models/file_model.dart';
import '../services/api_service.dart';
import '../core/constants/api_endpoints.dart';

class BookmarkProvider extends ChangeNotifier {
  bool _isLoading = false;
  List<AcademicFileModel> _bookmarks = [];
  Set<int> _bookmarkedFileIds = {};

  bool get isLoading => _isLoading;
  List<AcademicFileModel> get bookmarks => _bookmarks;

  bool isBookmarked(int fileId) => _bookmarkedFileIds.contains(fileId);

  Future<void> fetchBookmarks(int userId) async {
    _isLoading = true;
    notifyListeners();

    try {
      final res = await ApiService.get(ApiEndpoints.bookmarks, queryParams: {'user_id': userId.toString()});
      if (res['status'] == 'success' && res['data'] != null) {
        final list = res['data'] as List? ?? [];
        _bookmarks = list.map((e) => AcademicFileModel.fromJson(e)).toList();
        _bookmarkedFileIds = _bookmarks.map((f) => f.id).toSet();
      }
    } catch (e) {
      debugPrint('Error fetching bookmarks: $e');
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<bool> toggleBookmark(int userId, int fileId) async {
    final wasBookmarked = _bookmarkedFileIds.contains(fileId);
    if (wasBookmarked) {
      _bookmarkedFileIds.remove(fileId);
      _bookmarks.removeWhere((f) => f.id == fileId);
    } else {
      _bookmarkedFileIds.add(fileId);
    }
    notifyListeners();

    try {
      final res = await ApiService.post(ApiEndpoints.toggleBookmark, {
        'user_id': userId,
        'file_id': fileId,
      });

      if (res['status'] == 'success') {
        fetchBookmarks(userId);
        return true;
      }
    } catch (e) {
      debugPrint('Error toggling bookmark: $e');
      if (wasBookmarked) {
        _bookmarkedFileIds.add(fileId);
      } else {
        _bookmarkedFileIds.remove(fileId);
      }
      notifyListeners();
    }
    return false;
  }
}
