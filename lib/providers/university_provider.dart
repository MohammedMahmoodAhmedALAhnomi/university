import 'package:flutter/material.dart';
import '../models/announcement.dart';
import '../models/major.dart';
import '../models/level_semester.dart';
import '../models/course.dart';
import '../models/file_item.dart';
import '../services/api_service.dart';

class UniversityProvider extends ChangeNotifier {
  // Home Data
  List<AnnouncementModel> _pinnedAnnouncements = [];
  List<MajorModel> _majors = [];
  List<FileItemModel> _recentFiles = [];
  Map<String, dynamic> _stats = {};
  Map<String, dynamic> _settings = {};
  bool _isHomeLoading = false;
  String? _homeError;

  // Major Details Data
  MajorModel? _currentMajor;
  List<LevelModel> _levels = [];
  List<SemesterModel> _semesters = [];
  List<CourseModel> _majorCourses = [];
  bool _isMajorLoading = false;

  // Course Details Data
  CourseModel? _currentCourse;
  List<FileItemModel> _courseFiles = [];
  Map<String, List<FileItemModel>> _categorizedFiles = {};
  bool _isCourseLoading = false;

  // Announcements Data
  List<AnnouncementModel> _announcements = [];
  bool _isAnnouncementsLoading = false;

  // Search Data
  List<CourseModel> _searchResultsCourses = [];
  List<FileItemModel> _searchResultsFiles = [];
  bool _isSearchLoading = false;

  // Getters
  List<AnnouncementModel> get pinnedAnnouncements => _pinnedAnnouncements;
  List<MajorModel> get majors => _majors;
  List<FileItemModel> get recentFiles => _recentFiles;
  Map<String, dynamic> get stats => _stats;
  Map<String, dynamic> get settings => _settings;
  bool get isHomeLoading => _isHomeLoading;
  String? get homeError => _homeError;

  MajorModel? get currentMajor => _currentMajor;
  List<LevelModel> get levels => _levels;
  List<SemesterModel> get semesters => _semesters;
  List<CourseModel> get majorCourses => _majorCourses;
  bool get isMajorLoading => _isMajorLoading;

  CourseModel? get currentCourse => _currentCourse;
  List<FileItemModel> get courseFiles => _courseFiles;
  Map<String, List<FileItemModel>> get categorizedFiles => _categorizedFiles;
  bool get isCourseLoading => _isCourseLoading;

  List<AnnouncementModel> get announcements => _announcements;
  bool get isAnnouncementsLoading => _isAnnouncementsLoading;

  List<CourseModel> get searchResultsCourses => _searchResultsCourses;
  List<FileItemModel> get searchResultsFiles => _searchResultsFiles;
  bool get isSearchLoading => _isSearchLoading;

  Future<void> fetchHomeData() async {
    _isHomeLoading = true;
    _homeError = null;
    notifyListeners();

    try {
      final res = await ApiService.get('/home');
      if (res['status'] == 'success') {
        final data = res['data'];
        _pinnedAnnouncements = (data['pinned_announcements'] as List? ?? [])
            .map((item) => AnnouncementModel.fromJson(item))
            .toList();
        _majors = (data['majors'] as List? ?? [])
            .map((item) => MajorModel.fromJson(item))
            .toList();
        _recentFiles = (data['recent_files'] as List? ?? [])
            .map((item) => FileItemModel.fromJson(item))
            .toList();
        _stats = data['stats'] ?? {};
        _settings = data['settings'] ?? {};
      }
    } catch (e) {
      _homeError = e.toString();
    }

    _isHomeLoading = false;
    notifyListeners();
  }

  Future<void> fetchMajorDetails(int majorId) async {
    _isMajorLoading = true;
    _currentMajor = null;
    _levels = [];
    _semesters = [];
    _majorCourses = [];
    notifyListeners();

    try {
      final res = await ApiService.get('/majors/$majorId');
      if (res['status'] == 'success') {
        final data = res['data'];
        _currentMajor = MajorModel.fromJson(data['major']);
        _levels = (data['levels'] as List? ?? [])
            .map((item) => LevelModel.fromJson(item))
            .toList();
        _semesters = (data['semesters'] as List? ?? [])
            .map((item) => SemesterModel.fromJson(item))
            .toList();
        _majorCourses = (data['courses'] as List? ?? [])
            .map((item) => CourseModel.fromJson(item))
            .toList();
      }
    } catch (e) {
      debugPrint('Error fetching major: $e');
    }

    _isMajorLoading = false;
    notifyListeners();
  }

  Future<void> fetchCourseDetails(int courseId) async {
    _isCourseLoading = true;
    _currentCourse = null;
    _courseFiles = [];
    _categorizedFiles = {};
    notifyListeners();

    try {
      final res = await ApiService.get('/courses/$courseId');
      if (res['status'] == 'success') {
        final data = res['data'];
        _currentCourse = CourseModel.fromJson(data['course']);
        _courseFiles = (data['all_files'] as List? ?? [])
            .map((item) => FileItemModel.fromJson(item))
            .toList();

        final catMap = data['categorized_files'] as Map<String, dynamic>? ?? {};
        catMap.forEach((key, list) {
          if (list is List) {
            _categorizedFiles[key] = list.map((i) => FileItemModel.fromJson(i)).toList();
          }
        });
      }
    } catch (e) {
      debugPrint('Error fetching course: $e');
    }

    _isCourseLoading = false;
    notifyListeners();
  }

  Future<bool> rateCourse(int courseId, int ratingValue) async {
    try {
      final res = await ApiService.post('/courses/$courseId/rate', {
        'rating': ratingValue,
      });
      if (res['status'] == 'success') {
        if (_currentCourse != null && _currentCourse!.id == courseId) {
          final data = res['data'];
          _currentCourse = CourseModel(
            id: _currentCourse!.id,
            majorId: _currentCourse!.majorId,
            levelId: _currentCourse!.levelId,
            semesterId: _currentCourse!.semesterId,
            name: _currentCourse!.name,
            description: _currentCourse!.description,
            majorName: _currentCourse!.majorName,
            levelName: _currentCourse!.levelName,
            semesterName: _currentCourse!.semesterName,
            avgRating: (data['avg_rating'] ?? 0).toDouble(),
            ratingCount: data['rating_count'] is int ? data['rating_count'] : int.parse((data['rating_count'] ?? 0).toString()),
            filesCount: _currentCourse!.filesCount,
          );
          notifyListeners();
        }
        return true;
      }
    } catch (e) {
      debugPrint('Error rating course: $e');
    }
    return false;
  }

  Future<void> fetchAnnouncements() async {
    _isAnnouncementsLoading = true;
    notifyListeners();

    try {
      final res = await ApiService.get('/announcements');
      if (res['status'] == 'success') {
        _announcements = (res['data'] as List? ?? [])
            .map((item) => AnnouncementModel.fromJson(item))
            .toList();
      }
    } catch (e) {
      debugPrint('Error fetching announcements: $e');
    }

    _isAnnouncementsLoading = false;
    notifyListeners();
  }

  Future<void> search(String query, {int? majorId, int? levelId, int? semesterId}) async {
    _isSearchLoading = true;
    _searchResultsCourses = [];
    _searchResultsFiles = [];
    notifyListeners();

    try {
      String path = '/search?q=${Uri.encodeComponent(query)}';
      if (majorId != null) path += '&major_id=$majorId';
      if (levelId != null) path += '&level_id=$levelId';
      if (semesterId != null) path += '&semester_id=$semesterId';

      final res = await ApiService.get(path);
      if (res['status'] == 'success') {
        final data = res['data'];
        _searchResultsCourses = (data['courses'] as List? ?? [])
            .map((item) => CourseModel.fromJson(item))
            .toList();
        _searchResultsFiles = (data['files'] as List? ?? [])
            .map((item) => FileItemModel.fromJson(item))
            .toList();
      }
    } catch (e) {
      debugPrint('Search error: $e');
    }

    _isSearchLoading = false;
    notifyListeners();
  }
}
