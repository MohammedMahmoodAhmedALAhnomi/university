import 'package:flutter/material.dart';
import '../models/major_model.dart';
import '../models/course_model.dart';
import '../models/file_model.dart';
import '../models/announcement_model.dart';
import '../services/api_service.dart';
import '../core/constants/api_endpoints.dart';

class AcademicProvider extends ChangeNotifier {
  bool _isLoadingHome = false;
  bool _isLoadingMajors = false;
  bool _isLoadingCourseDetails = false;

  Map<String, dynamic>? _homeStats;
  List<AnnouncementModel> _pinnedAnnouncements = [];
  List<AcademicFileModel> _recentFiles = [];

  List<MajorModel> _majors = [];
  MajorModel? _selectedMajor;
  List<CourseModel> _majorCourses = [];

  CourseModel? _currentCourse;
  List<AcademicFileModel> _courseFiles = [];
  Map<String, List<AcademicFileModel>> _categorizedFiles = {};

  bool get isLoadingHome => _isLoadingHome;
  bool get isLoadingMajors => _isLoadingMajors;
  bool get isLoadingCourseDetails => _isLoadingCourseDetails;

  Map<String, dynamic>? get homeStats => _homeStats;
  List<AnnouncementModel> get pinnedAnnouncements => _pinnedAnnouncements;
  List<AcademicFileModel> get recentFiles => _recentFiles;
  List<MajorModel> get majors => _majors;

  MajorModel? get selectedMajor => _selectedMajor;
  List<CourseModel> get majorCourses => _majorCourses;

  CourseModel? get currentCourse => _currentCourse;
  List<AcademicFileModel> get courseFiles => _courseFiles;
  Map<String, List<AcademicFileModel>> get categorizedFiles => _categorizedFiles;

  Future<void> fetchHomeData() async {
    _isLoadingHome = true;
    notifyListeners();

    try {
      final res = await ApiService.get(ApiEndpoints.home);
      if (res['status'] == 'success' && res['data'] != null) {
        final data = res['data'];
        _homeStats = data['stats'];
        _pinnedAnnouncements = (data['pinned_announcements'] as List? ?? [])
            .map((e) => AnnouncementModel.fromJson(e))
            .toList();
        _recentFiles = (data['recent_files'] as List? ?? [])
            .map((e) => AcademicFileModel.fromJson(e))
            .toList();
        _majors = (data['majors'] as List? ?? [])
            .map((e) => MajorModel.fromJson(e))
            .toList();
      }
    } catch (e) {
      debugPrint('Error fetching home: $e');
    }

    _isLoadingHome = false;
    notifyListeners();
  }

  Future<void> fetchMajors() async {
    _isLoadingMajors = true;
    notifyListeners();

    try {
      final res = await ApiService.get(ApiEndpoints.majors);
      if (res['status'] == 'success' && res['data'] != null) {
        _majors = (res['data'] as List).map((e) => MajorModel.fromJson(e)).toList();
      }
    } catch (e) {
      debugPrint('Error fetching majors: $e');
    }

    _isLoadingMajors = false;
    notifyListeners();
  }

  Future<void> fetchMajorDetails(int majorId) async {
    _isLoadingMajors = true;
    notifyListeners();

    try {
      final res = await ApiService.get('${ApiEndpoints.majorDetails}$majorId');
      if (res['status'] == 'success' && res['data'] != null) {
        final data = res['data'];
        if (data['major'] != null) {
          _selectedMajor = MajorModel.fromJson(data['major']);
        }
        if (data['courses'] != null) {
          _majorCourses = (data['courses'] as List).map((e) => CourseModel.fromJson(e)).toList();
        }
      }
    } catch (e) {
      debugPrint('Error fetching major details: $e');
    }

    _isLoadingMajors = false;
    notifyListeners();
  }

  Future<void> fetchCourseDetails(int courseId) async {
    _isLoadingCourseDetails = true;
    notifyListeners();

    try {
      final res = await ApiService.get('${ApiEndpoints.courseDetails}$courseId');
      if (res['status'] == 'success' && res['data'] != null) {
        final data = res['data'];
        if (data['course'] != null) {
          _currentCourse = CourseModel.fromJson(data['course']);
        }
        if (data['all_files'] != null) {
          _courseFiles = (data['all_files'] as List).map((e) => AcademicFileModel.fromJson(e)).toList();
        }
        if (data['categorized_files'] != null) {
          _categorizedFiles = {};
          (data['categorized_files'] as Map<String, dynamic>).forEach((key, val) {
            _categorizedFiles[key] = (val as List).map((e) => AcademicFileModel.fromJson(e)).toList();
          });
        }
      }
    } catch (e) {
      debugPrint('Error fetching course details: $e');
    }

    _isLoadingCourseDetails = false;
    notifyListeners();
  }

  Future<bool> rateCourse(int courseId, int rating) async {
    try {
      final res = await ApiService.post('${ApiEndpoints.rateCourse}$courseId/rate', {
        'rating': rating,
      });
      if (res['status'] == 'success') {
        fetchCourseDetails(courseId);
        return true;
      }
    } catch (e) {
      debugPrint('Error rating course: $e');
    }
    return false;
  }
}
