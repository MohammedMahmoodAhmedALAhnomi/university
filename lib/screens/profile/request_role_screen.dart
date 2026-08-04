import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../providers/academic_provider.dart';
import '../../core/constants/app_colors.dart';
import '../../services/api_service.dart';
import '../../core/constants/api_endpoints.dart';

class RequestRoleScreen extends StatefulWidget {
  const RequestRoleScreen({super.key});

  @override
  State<RequestRoleScreen> createState() => _RequestRoleScreenState();
}

class _RequestRoleScreenState extends State<RequestRoleScreen> {
  final _formKey = GlobalKey<FormState>();
  final _reasonController = TextEditingController();
  int? _selectedMajorId;
  int? _selectedLevelId;
  String _accountType = 'representative';
  bool _isSubmitting = false;

  @override
  void initState() {
    super.initState();
    Future.microtask(() {
      if (!mounted) return;
      Provider.of<AcademicProvider>(context, listen: false).fetchMajors();
    });
  }

  @override
  void dispose() {
    _reasonController.dispose();
    super.dispose();
  }

  void _submit() async {
    if (!_formKey.currentState!.validate()) return;
    final auth = Provider.of<AuthProvider>(context, listen: false);

    if (_selectedMajorId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('يرجى تحديد التخصص الدراسي')),
      );
      return;
    }

    setState(() => _isSubmitting = true);

    try {
      final res = await ApiService.post(ApiEndpoints.requestRole, {
        'user_id': auth.user!.id,
        'major_id': _selectedMajorId,
        'level_id': _selectedLevelId,
        'account_type': _accountType,
        'reason': _reasonController.text.trim(),
      });

      if (!mounted) return;

      if (res['status'] == 'success') {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(res['message'] ?? 'تم إرسال طلب الترقية بنجاح'), backgroundColor: AppColors.success),
        );
        Navigator.of(context).pop();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(res['message'] ?? 'فشل إرسال الطلب'), backgroundColor: AppColors.error),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('خطأ: $e'), backgroundColor: AppColors.error),
        );
      }
    }

    setState(() => _isSubmitting = false);
  }

  @override
  Widget build(BuildContext context) {
    final academic = Provider.of<AcademicProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('طلب ترقية إلى مندوب / مدير'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const Text(
                'طلب الانضمام كـ مندوب دفعة أو مدير تخصص',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 8),
              Text(
                'يتيح لك هذا الطلب الحصول على صلاحية إدارة رفع الجداول الدراسية والملفات الخاصة بدفعتك بعد مراجعة الإدارة.',
                style: TextStyle(color: Colors.grey[600], fontSize: 13, height: 1.4),
              ),
              const SizedBox(height: 24),

              DropdownButtonFormField<int>(
                initialValue: _selectedMajorId,
                decoration: InputDecoration(
                  labelText: 'التخصص الدراسي',
                  prefixIcon: const Icon(Icons.school_outlined),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                ),
                items: academic.majors.map((m) {
                  return DropdownMenuItem<int>(
                    value: m.id,
                    child: Text(m.name),
                  );
                }).toList(),
                onChanged: (val) => setState(() => _selectedMajorId = val),
              ),
              const SizedBox(height: 16),

              DropdownButtonFormField<String>(
                initialValue: _accountType,
                decoration: InputDecoration(
                  labelText: 'نوع الصلاحية المطلوبة',
                  prefixIcon: const Icon(Icons.badge_outlined),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                ),
                items: const [
                  DropdownMenuItem(value: 'representative', child: Text('مندوب مستوى / دفعة')),
                  DropdownMenuItem(value: 'major_admin', child: Text('مشرف تخصص أكاديمي كامل')),
                ],
                onChanged: (val) {
                  if (val != null) setState(() => _accountType = val);
                },
              ),
              const SizedBox(height: 16),

              if (_accountType == 'representative') ...[
                DropdownButtonFormField<int>(
                  initialValue: _selectedLevelId,
                  decoration: InputDecoration(
                    labelText: 'المستوى الدراسي',
                    prefixIcon: const Icon(Icons.format_list_numbered_rounded),
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  items: const [
                    DropdownMenuItem(value: 1, child: Text('المستوى 1')),
                    DropdownMenuItem(value: 2, child: Text('المستوى 2')),
                    DropdownMenuItem(value: 3, child: Text('المستوى 3')),
                    DropdownMenuItem(value: 4, child: Text('المستوى 4')),
                  ],
                  onChanged: (val) => setState(() => _selectedLevelId = val),
                ),
                const SizedBox(height: 16),
              ],

              TextFormField(
                controller: _reasonController,
                maxLines: 3,
                decoration: InputDecoration(
                  labelText: 'سبب أو تفاصيل الطلب',
                  prefixIcon: const Icon(Icons.info_outline),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                ),
                validator: (val) {
                  if (val == null || val.isEmpty) return 'يرجى كتابة سبب تقديم الطلب';
                  return null;
                },
              ),
              const SizedBox(height: 24),

              ElevatedButton(
                onPressed: _isSubmitting ? null : _submit,
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.primary,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: _isSubmitting
                    ? const SizedBox(
                        height: 20,
                        width: 20,
                        child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
                      )
                    : const Text('إرسال الطلب للمراجعة', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
