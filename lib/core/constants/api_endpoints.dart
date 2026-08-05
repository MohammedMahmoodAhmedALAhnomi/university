class ApiEndpoints {
  // Base Server URL
  static const String baseUrl = "https://university-production-102b.up.railway.app/api";
  static const String webBaseUrl = "https://university-production-102b.up.railway.app/api";
  static const String serverHost = "https://university-production-102b.up.railway.app";

  static const String login = "/login";
  static const String register = "/register";
  static const String home = "/home";
  static const String majors = "/majors";
  static const String majorDetails = "/majors/";
  static const String courseDetails = "/courses/";
  static const String rateCourse = "/courses/"; // /{id}/rate
  static const String announcements = "/announcements";
  static const String search = "/search";
  static const String downloadFile = "/files/"; // /{id}/download
  static const String requestRole = "/request-role";
  static const String profile = "/profile";
  static const String updateProfile = "/profile/update";
  static const String settings = "/settings";
  static const String about = "/about";
  static const String contact = "/contact";

  // Notifications Endpoints
  static const String notifications = "/notifications";
  static const String markNotificationRead = "/notifications/"; // /{id}/read
  static const String readAllNotifications = "/notifications/read-all";

  // Bookmarks Endpoints
  static const String bookmarks = "/bookmarks";
  static const String toggleBookmark = "/bookmarks/toggle";

  // Admin Endpoints
  static const String adminRequests = "/admin/requests";
  static const String approveRequest = "/admin/requests/"; // /{id}/approve
  static const String rejectRequest = "/admin/requests/"; // /{id}/reject
  static const String adminStats = "/admin/stats";
  static const String createAnnouncement = "/admin/announcements/create";
  static const String deleteAnnouncement = "/admin/announcements/"; // /{id}/delete

  // File Upload & Content Management
  static const String uploadFile = "/files/upload";
  static const String adminFiles = "/admin/files";
  static const String approveFile = "/admin/files/"; // /{id}/approve
  static const String deleteFile = "/admin/files/"; // /{id}/delete

  // Major & Course Management
  static const String createMajor = "/admin/majors/create";
  static const String deleteMajor = "/admin/majors/"; // /{id}/delete
  static const String adminCourses = "/admin/courses";
  static const String createCourse = "/admin/courses/create";
  static const String deleteCourse = "/admin/courses/"; // /{id}/delete

  // User Management
  static const String adminUsers = "/admin/users";
  static const String updateUserRole = "/admin/users/"; // /{id}/role
}

