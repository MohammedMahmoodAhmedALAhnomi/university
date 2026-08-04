import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../providers/auth_provider.dart';
import '../../services/api_service.dart';
import '../../core/constants/api_endpoints.dart';
import '../../core/constants/app_colors.dart';
import '../../core/utils/ui_helpers.dart';

class AdminDashboardScreen extends StatefulWidget {
  const AdminDashboardScreen({super.key});

  @override
  State<AdminDashboardScreen> createState() => _AdminDashboardScreenState();
}

class _AdminDashboardScreenState extends State<AdminDashboardScreen> {
  bool _isLoading = true;
  List _requests = [];
  Map<String, dynamic> _stats = {
    'users': 0,
    'majors': 0,
    'courses': 0,
    'files': 0,
    'pending_requests': 0,
    'announcements': 0,
  };

  @override
  void initState() {
    super.initState();
    _fetchDashboardData();
  }

  Future<void> _fetchDashboardData() async {
    if (!mounted) return;
    setState(() => _isLoading = true);
    await Future.wait([
      _fetchRequests(),
      _fetchStats(),
    ]);
    if (!mounted) return;
    setState(() => _isLoading = false);
  }


  Future<void> _fetchRequests() async {
    try {
      final res = await ApiService.get(ApiEndpoints.adminRequests);
      if (res['status'] == 'success' && res['data'] != null) {
        _requests = res['data'] as List;
      }
    } catch (e) {
      debugPrint('Error fetching admin requests: $e');
    }
  }

  Future<void> _fetchStats() async {
    try {
      final res = await ApiService.get(ApiEndpoints.adminStats);
      if (res['status'] == 'success' && res['data'] != null) {
        if (mounted) {
          setState(() {
            _stats = Map<String, dynamic>.from(res['data']);
          });
        }
      }
    } catch (e) {
      debugPrint('Error fetching admin stats: $e');
    }
  }


  Future<void> _handleApprove(int requestId) async {
    try {
      final res = await ApiService.post('${ApiEndpoints.approveRequest}$requestId/approve', {});
      if (mounted) {
        if (res['status'] == 'success') {
          UiHelpers.showSnackBar(context, message: 'تم قبول الطلب وترقية المستخدم بنجاح');
          _fetchDashboardData();
        } else {
          UiHelpers.showSnackBar(context, message: res['message'] ?? 'تعذر قبول الطلب', isError: true);
        }
      }
    } catch (e) {
      if (mounted) {
        UiHelpers.showSnackBar(context, message: 'حدث خطأ أثناء تنفيذ الطلب', isError: true);
      }
    }
  }

  Future<void> _handleReject(int requestId) async {
    try {
      final res = await ApiService.post('${ApiEndpoints.rejectRequest}$requestId/reject', {});
      if (mounted) {
        if (res['status'] == 'success') {
          UiHelpers.showSnackBar(context, message: 'تم رفض الطلب بنجاح', isError: false);
          _fetchDashboardData();
        } else {
          UiHelpers.showSnackBar(context, message: res['message'] ?? 'تعذر رفض الطلب', isError: true);
        }
      }
    } catch (e) {
      if (mounted) {
        UiHelpers.showSnackBar(context, message: 'حدث خطأ أثناء تنفيذ الطلب', isError: true);
      }
    }
  }

  void _showCreateAnnouncementDialog() {
    final titleController = TextEditingController();
    final contentController = TextEditingController();
    String type = 'info';

    showDialog(
      context: context,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setDialogState) {
            return AlertDialog(
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
              title: const Row(
                children: [
                  Icon(Icons.campaign_rounded, color: AppColors.primary),
                  SizedBox(width: 8),
                  Text('نشر إعلان جديد للطلاب', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                ],
              ),
              content: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    TextField(
                      controller: titleController,
                      decoration: const InputDecoration(
                        labelText: 'عنوان الإعلان',
                        hintText: 'مثال: جدول الاختبارات النهائية',
                      ),
                    ),
                    const SizedBox(height: 12),
                    TextField(
                      controller: contentController,
                      maxLines: 3,
                      decoration: const InputDecoration(
                        labelText: 'تفاصيل ومحتوى الإعلان',
                        hintText: 'اكتب نص الإعلان والتفاصيل...',
                      ),
                    ),
                    const SizedBox(height: 16),
                    DropdownButtonFormField<String>(
                      initialValue: type,
                      decoration: const InputDecoration(labelText: 'نوع الإعلان والتنفيذي'),
                      items: const [
                        DropdownMenuItem(value: 'info', child: Text('معلومات وتنبيه عام (عادي)')),
                        DropdownMenuItem(value: 'warning', child: Text('تنبيه هام عاجل (تحذير)')),
                        DropdownMenuItem(value: 'success', child: Text('تهنئة وتحديث سار (نجاح)')),
                      ],
                      onChanged: (val) {
                        if (val != null) setDialogState(() => type = val);
                      },
                    ),
                  ],
                ),
              ),
              actions: [
                TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text('إلغاء'),
                ),
                ElevatedButton.icon(
                  onPressed: () async {
                    if (titleController.text.trim().isEmpty || contentController.text.trim().isEmpty) {
                      UiHelpers.showSnackBar(context, message: 'يرجى تعبئة العنوان والمحتوى', isError: true);
                      return;
                    }
                    Navigator.pop(context);
                    final messenger = ScaffoldMessenger.of(this.context);
                    try {
                      final res = await ApiService.post(ApiEndpoints.createAnnouncement, {
                        'title': titleController.text.trim(),
                        'content': contentController.text.trim(),
                        'type': type,
                      });
                      if (res['status'] == 'success') {
                        messenger.showSnackBar(const SnackBar(content: Text('تم نشر الإعلان ونشر الإشعار للجميع 🎉')));
                        _fetchDashboardData();
                      } else {
                        messenger.showSnackBar(SnackBar(content: Text(res['message'] ?? 'تعذر نشر الإعلان')));
                      }
                    } catch (e) {
                      messenger.showSnackBar(SnackBar(content: Text('خطأ أثناء النشر: $e')));
                    }
                  },
                  icon: const Icon(Icons.send_rounded, size: 18),
                  label: const Text('نشر وتنبيه الطلاب'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                ),
              ],

            );
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final auth = Provider.of<AuthProvider>(context);
    final user = auth.user;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: AppBar(
        title: const Text('لوحة الإدارة والمندوبين'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh_rounded),
            tooltip: 'تحديث البيانات',
            onPressed: _fetchDashboardData,
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _fetchDashboardData,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header Role Card
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  gradient: AppColors.heroGradient,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [
                    BoxShadow(
                      color: AppColors.primary.withValues(alpha: 0.3),
                      blurRadius: 12,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        const Icon(Icons.admin_panel_settings_rounded, color: Colors.amber, size: 28),
                        const SizedBox(width: 8),
                        Text(
                          user != null ? _getRoleTitle(user.role) : 'لوحة التحكم',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    const Text(
                      'مرحباً بك في لوحة تحكم المنصة. يمكنك متابعة الإحصائيات الحية، قبول طلبات المندوبين، ونشر التنبيهات للطلاب.',
                      style: TextStyle(color: Colors.white70, fontSize: 13, height: 1.4),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 20),

              // Statistics Section Header
              const Text('إحصائيات النظام الحية', style: TextStyle(fontSize: 17, fontWeight: FontWeight.bold)),
              const SizedBox(height: 12),

              // Stats Grid
              GridView.count(
                crossAxisCount: 3,
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                mainAxisSpacing: 10,
                crossAxisSpacing: 10,
                childAspectRatio: 1.1,
                children: [
                  _buildStatCard('المستخدمين', '${_stats['users']}', Icons.people_outline_rounded, AppColors.primary, isDark),
                  _buildStatCard('التخصصات', '${_stats['majors']}', Icons.school_outlined, AppColors.secondary, isDark),
                  _buildStatCard('المواد', '${_stats['courses']}', Icons.menu_book_rounded, Colors.purple, isDark),
                  _buildStatCard('الملفات', '${_stats['files']}', Icons.folder_copy_outlined, Colors.orange, isDark),
                  _buildStatCard('الإعلانات', '${_stats['announcements']}', Icons.campaign_outlined, Colors.teal, isDark),
                  _buildStatCard('طلبات المعلقة', '${_stats['pending_requests']}', Icons.pending_actions_rounded, Colors.redAccent, isDark),
                ],
              ),
              const SizedBox(height: 24),

              // Quick Actions Row
              const Text('أدوات الإدارة السريعة', style: TextStyle(fontSize: 17, fontWeight: FontWeight.bold)),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: ElevatedButton.icon(
                      onPressed: _showCreateAnnouncementDialog,
                      icon: const Icon(Icons.campaign_rounded, color: Colors.white, size: 20),
                      label: const Text('نشر إعلان جديد', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.primary,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: () async {
                        final url = '${ApiEndpoints.serverHost}/admin/dashboard';
                        final uri = Uri.parse(url);
                        if (await canLaunchUrl(uri)) {
                          await launchUrl(uri, mode: LaunchMode.externalApplication);
                        } else {
                          if (context.mounted) {
                            UiHelpers.showSnackBar(context, message: 'تعذر فتح اللوحة الكاملة', isError: true);
                          }
                        }
                      },
                      icon: const Icon(Icons.open_in_browser_rounded, size: 20),
                      label: const Text('اللوحة الكاملة', style: TextStyle(fontWeight: FontWeight.bold)),
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 28),

              // Pending Requests Section
              const Row(
                children: [
                  Icon(Icons.rule_folder_rounded, color: AppColors.primary),
                  SizedBox(width: 8),
                  Text(
                    'طلبات الانضمام والترقية للمندوبين',
                    style: TextStyle(fontSize: 17, fontWeight: FontWeight.bold),
                  ),
                ],
              ),
              const SizedBox(height: 12),

              _isLoading
                  ? Column(
                      children: [
                        UiHelpers.buildSkeletonLoader(height: 120, borderRadius: 16),
                        UiHelpers.buildSkeletonLoader(height: 120, borderRadius: 16),
                      ],
                    )
                  : _requests.isEmpty
                      ? UiHelpers.buildEmptyState(
                          icon: Icons.assignment_turned_in_rounded,
                          title: 'لا توجد طلبات معلقة',
                          subtitle: 'جميع طلبات ترقية المندوبين والمشرفين مراجعة ومحدثة حالياً',
                        )
                      : ListView.builder(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: _requests.length,
                          itemBuilder: (context, index) {
                            final req = _requests[index];
                            final isPending = req['status'] == 'pending';

                            return Card(
                              margin: const EdgeInsets.only(bottom: 14),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                              elevation: 2,
                              child: Padding(
                                padding: const EdgeInsets.all(16),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                      children: [
                                        CircleAvatar(
                                          backgroundColor: AppColors.primary.withValues(alpha: 0.1),
                                          child: const Icon(Icons.person_rounded, color: AppColors.primary),
                                        ),
                                        const SizedBox(width: 12),
                                        Expanded(
                                          child: Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                req['user_name'] ?? 'مستخدم',
                                                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
                                              ),
                                              Text(
                                                req['user_email'] ?? '',
                                                style: TextStyle(color: Colors.grey[600], fontSize: 12),
                                              ),
                                            ],
                                          ),
                                        ),
                                        _buildStatusBadge(req['status'] ?? 'pending'),
                                      ],
                                    ),
                                    const Divider(height: 20),
                                    Row(
                                      children: [
                                        const Icon(Icons.school_outlined, size: 16, color: Colors.grey),
                                        const SizedBox(width: 6),
                                        Text('التخصص: ${req['major_name'] ?? "-"}', style: const TextStyle(fontSize: 13)),
                                      ],
                                    ),
                                    const SizedBox(height: 4),
                                    Row(
                                      children: [
                                        const Icon(Icons.layers_outlined, size: 16, color: Colors.grey),
                                        const SizedBox(width: 6),
                                        Text('الرتبة المطلوبة: ${req['account_type_arabic'] ?? "-"} • ${req['level_name'] ?? "جميع المستويات"}', style: const TextStyle(fontSize: 13)),
                                      ],
                                    ),
                                    if (req['notes'] != null && req['notes'].toString().isNotEmpty) ...[
                                      const SizedBox(height: 6),
                                      Text(
                                        'ملاحظات الطالب: ${req['notes']}',
                                        style: TextStyle(fontSize: 12, color: Colors.grey[700], fontStyle: FontStyle.italic),
                                      ),
                                    ],
                                    if (isPending) ...[
                                      const SizedBox(height: 16),
                                      Row(
                                        children: [
                                          Expanded(
                                            child: ElevatedButton.icon(
                                              onPressed: () => _handleApprove(req['id']),
                                              icon: const Icon(Icons.check_rounded, color: Colors.white, size: 18),
                                              label: const Text('موافقة وقبول', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                                              style: ElevatedButton.styleFrom(
                                                backgroundColor: Colors.green,
                                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                              ),
                                            ),
                                          ),
                                          const SizedBox(width: 10),
                                          Expanded(
                                            child: OutlinedButton.icon(
                                              onPressed: () => _handleReject(req['id']),
                                              icon: const Icon(Icons.close_rounded, color: Colors.redAccent, size: 18),
                                              label: const Text('رفض الطلب', style: TextStyle(color: Colors.redAccent, fontWeight: FontWeight.bold)),
                                              style: OutlinedButton.styleFrom(
                                                side: const BorderSide(color: Colors.redAccent),
                                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                              ),
                                            ),
                                          ),
                                        ],
                                      ),
                                    ],
                                  ],
                                ),
                              ),
                            );
                          },
                        ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStatCard(String title, String count, IconData icon, Color color, bool isDark) {
    return Container(
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: isDark ? AppColors.cardDark : Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: color.withValues(alpha: 0.25)),
        boxShadow: [
          BoxShadow(
            color: color.withValues(alpha: 0.08),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(icon, color: color, size: 22),
          const SizedBox(height: 4),
          Text(
            count,
            style: TextStyle(color: color, fontSize: 18, fontWeight: FontWeight.bold),
          ),
          Text(
            title,
            style: TextStyle(fontSize: 10, color: Colors.grey[600]),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  Widget _buildStatusBadge(String status) {
    Color bg;
    Color fg;
    String text;

    switch (status) {
      case 'approved':
        bg = Colors.green.withValues(alpha: 0.15);
        fg = Colors.green;
        text = 'مقبول';
        break;
      case 'rejected':
        bg = Colors.red.withValues(alpha: 0.15);
        fg = Colors.red;
        text = 'مرفوض';
        break;
      default:
        bg = Colors.amber.withValues(alpha: 0.15);
        fg = Colors.amber[800]!;
        text = 'معلق للمراجعة';
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Text(
        text,
        style: TextStyle(color: fg, fontWeight: FontWeight.bold, fontSize: 11),
      ),
    );
  }

  String _getRoleTitle(String role) {
    switch (role) {
      case 'admin':
        return 'مدير النظام الشامل';
      case 'major_admin':
        return 'مشرف تخصص أكاديمي';
      case 'manager':
        return 'مندوب مستوى ودفعة';
      default:
        return 'لوحة التحكم';
    }
  }
}
