import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:file_picker/file_picker.dart';
import '../../providers/auth_provider.dart';
import '../../services/api_service.dart';
import '../../core/constants/api_endpoints.dart';
import '../../core/constants/app_colors.dart';
import '../../core/utils/ui_helpers.dart';

class UploadFileScreen extends StatefulWidget {
  final int? initialCourseId;
  final String? initialCourseName;

  const UploadFileScreen({
    super.key,
    this.initialCourseId,
    this.initialCourseName,
  });

  @override
  State<UploadFileScreen> createState() => _UploadFileScreenState();
}

class _UploadFileScreenState extends State<UploadFileScreen> {
  final _formKey = GlobalKey<FormState>();
  final _titleController = TextEditingController();
  final _descriptionController = TextEditingController();

  List<dynamic> _majors = [];
  List<dynamic> _courses = [];
  int? _selectedMajorId;
  int? _selectedCourseId;
  String _selectedCategory = 'summaries';
  String _selectedFileType = 'summary';

  PlatformFile? _pickedFile;
  bool _isLoadingMajors = false;
  bool _isLoadingCourses = false;
  bool _isUploading = false;

  final Map<String, String> _categories = {
    'lectures': 'محاضرات دراسية 📖',
    'summaries': 'ملخصات وملازم 📝',
    'exams': 'نماذج اختيارات واسئلة ✍️',
    'models': 'مشاريع ومراجع 📚',
  };

  final Map<String, String> _fileTypes = {
    'lecture': 'محاضرة (Lecture)',
    'summary': 'ملخص (Summary)',
    'exam': 'امتحان / اختبار (Exam)',
    'other': 'مرجع / ملف آخر (Other)',
  };

  @override
  void initState() {
    super.initState();
    _selectedCourseId = widget.initialCourseId;
    _fetchMajors();
  }

  Future<void> _fetchMajors() async {
    setState(() => _isLoadingMajors = true);
    try {
      final res = await ApiService.get(ApiEndpoints.majors);
      if (res['status'] == 'success' && res['data'] != null) {
        if (mounted) {
          setState(() {
            _majors = res['data'] as List;
            final user = Provider.of<AuthProvider>(context, listen: false).user;
            if (user != null && user.majorId != null) {
              _selectedMajorId = user.majorId;
              _fetchCoursesForMajor(_selectedMajorId!);
            } else if (_majors.isNotEmpty) {
              _selectedMajorId = _majors.first['id'];
              _fetchCoursesForMajor(_selectedMajorId!);
            }
          });
        }
      }
    } catch (e) {
      debugPrint('Error fetching majors: $e');
    } finally {
      if (mounted) setState(() => _isLoadingMajors = false);
    }
  }

  Future<void> _fetchCoursesForMajor(int majorId) async {
    setState(() => _isLoadingCourses = true);
    try {
      final res = await ApiService.get('${ApiEndpoints.majorDetails}$majorId');
      if (res['status'] == 'success' &&
          res['data'] != null &&
          res['data']['courses'] != null) {
        if (mounted) {
          setState(() {
            _courses = res['data']['courses'] as List;
            if (_selectedCourseId != null) {
              final exists = _courses.any((c) => c['id'] == _selectedCourseId);
              if (!exists && _courses.isNotEmpty) {
                _selectedCourseId = _courses.first['id'];
              }
            } else if (_courses.isNotEmpty) {
              _selectedCourseId = _courses.first['id'];
            }
          });
        }
      }
    } catch (e) {
      debugPrint('Error fetching courses: $e');
    } finally {
      if (mounted) setState(() => _isLoadingCourses = false);
    }
  }

  Future<void> _pickFile() async {
    try {
      final result = await FilePicker.platform.pickFiles(
        type: FileType.custom,
        allowedExtensions: [
          'pdf',
          'doc',
          'docx',
          'ppt',
          'pptx',
          'zip',
          'png',
          'jpg',
          'jpeg',
        ],
        withData: kIsWeb,
      );

      if (result != null && result.files.isNotEmpty) {
        setState(() {
          _pickedFile = result.files.first;
          if (_titleController.text.trim().isEmpty) {
            final nameWithoutExt = _pickedFile!.name.split('.').first;
            _titleController.text = nameWithoutExt;
          }
        });
      }
    } catch (e) {
      if (mounted) {
        UiHelpers.showSnackBar(
          context,
          message: 'تعذر اختيار الملف: $e',
          isError: true,
        );
      }
    }
  }

  Future<void> _handleUpload() async {
    if (!_formKey.currentState!.validate()) return;
    if (_pickedFile == null) {
      UiHelpers.showSnackBar(
        context,
        message: 'يرجى اختيار ملف لرفعه',
        isError: true,
      );
      return;
    }
    if (_selectedCourseId == null) {
      UiHelpers.showSnackBar(
        context,
        message: 'يرجى اختيار المادة الدراسية',
        isError: true,
      );
      return;
    }

    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final user = authProvider.user;

    setState(() => _isUploading = true);

    try {
      final fields = {
        'title': _titleController.text.trim(),
        'course_id': _selectedCourseId.toString(),
        'file_type': _selectedFileType,
        'category': _selectedCategory,
        'description': _descriptionController.text.trim(),
        'user_id': user?.id.toString() ?? '0',
      };

      final res = await ApiService.uploadMultipart(
        ApiEndpoints.uploadFile,
        fields: fields,
        filePath: _pickedFile!.path,
        fileBytes: _pickedFile!.bytes,
        filename: _pickedFile!.name,
      );

      if (mounted) {
        if (res['status'] == 'success') {
          UiHelpers.showSnackBar(context, message: 'تم رفع الملف بنجاح 🎉');
          Navigator.pop(context, true);
        } else {
          UiHelpers.showSnackBar(
            context,
            message: res['message'] ?? 'تعذر رفع الملف',
            isError: true,
          );
        }
      }
    } catch (e) {
      if (mounted) {
        UiHelpers.showSnackBar(
          context,
          message: 'خطأ أثناء الرفع: $e',
          isError: true,
        );
      }
    } finally {
      if (mounted) setState(() => _isUploading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final isDelegateOrAdmin =
        authProvider.isDelegate ||
        authProvider.isAdmin ||
        authProvider.isMajorAdmin;

    return Scaffold(
      appBar: AppBar(
        title: const Text(
          'رفع ملف تعليمي جديد',
          style: TextStyle(fontWeight: FontWeight.bold),
        ),
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
      ),
      body: !isDelegateOrAdmin
          ? _buildAccessDeniedWidget(context)
          : SingleChildScrollView(
              padding: const EdgeInsets.all(20.0),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // Header card
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: AppColors.primary.withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(
                          color: AppColors.primary.withValues(alpha: 0.3),
                        ),
                      ),
                      child: Row(
                        children: [
                          const Icon(
                            Icons.cloud_upload_rounded,
                            color: AppColors.primary,
                            size: 32,
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Text(
                                  'رفع وتوثيق المحاضرات والملخصات 📤',
                                  style: TextStyle(
                                    fontSize: 16,
                                    fontWeight: FontWeight.bold,
                                    color: AppColors.primary,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                const Text(
                                  'صلاحية المندوب والمشرف: يمكنك نشر الملازم والملخصات للطلاب مباشرة.',
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: Colors.black87,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 20),

                    // File Selector Box
                    GestureDetector(
                      onTap: _pickFile,
                      child: Container(
                        height: 120,
                        decoration: BoxDecoration(
                          color: _pickedFile == null
                              ? Colors.grey.shade50
                              : AppColors.secondary.withValues(alpha: 0.1),
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(
                            color: _pickedFile == null
                                ? Colors.grey.shade300
                                : AppColors.secondary,
                            width: 2,
                          ),
                        ),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(
                              _pickedFile == null
                                  ? Icons.upload_file_rounded
                                  : Icons.check_circle_rounded,
                              size: 40,
                              color: _pickedFile == null
                                  ? AppColors.primary
                                  : AppColors.secondary,
                            ),
                            const SizedBox(height: 8),
                            Text(
                              _pickedFile == null
                                  ? 'انقر لااختيار ملف (PDF, Word, Images, ZIP)'
                                  : _pickedFile!.name,
                              style: TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.bold,
                                color: _pickedFile == null
                                    ? Colors.black87
                                    : AppColors.secondary,
                              ),
                              textAlign: TextAlign.center,
                            ),
                            if (_pickedFile != null)
                              Text(
                                '${(_pickedFile!.size / (1024 * 1024)).toStringAsFixed(2)} ميجابايت',
                                style: const TextStyle(
                                  fontSize: 12,
                                  color: Colors.grey,
                                ),
                              ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 20),

                    // Title
                    TextFormField(
                      controller: _titleController,
                      decoration: const InputDecoration(
                        labelText: 'عنوان الملف / الملخص *',
                        hintText: 'مثال: ملخص المحاضرة الأولى والمصفوفات',
                        prefixIcon: Icon(
                          Icons.title_rounded,
                          color: AppColors.primary,
                        ),
                      ),
                      validator: (val) => val == null || val.trim().isEmpty
                          ? 'يرجى كتابة عنوان الملف'
                          : null,
                    ),
                    const SizedBox(height: 16),

                    // Major Selection
                    _isLoadingMajors
                        ? const Center(child: CircularProgressIndicator())
                        : DropdownButtonFormField<int>(
                            value: _selectedMajorId,
                            decoration: const InputDecoration(
                              labelText: 'التخصص الدراسي *',
                              prefixIcon: Icon(
                                Icons.school_rounded,
                                color: AppColors.primary,
                              ),
                            ),
                            items: _majors.map((m) {
                              return DropdownMenuItem<int>(
                                value: m['id'],
                                child: Text(m['name'] ?? ''),
                              );
                            }).toList(),
                            onChanged: (val) {
                              if (val != null) {
                                setState(() {
                                  _selectedMajorId = val;
                                  _selectedCourseId = null;
                                });
                                _fetchCoursesForMajor(val);
                              }
                            },
                          ),
                    const SizedBox(height: 16),

                    // Course Selection
                    _isLoadingCourses
                        ? const Center(child: CircularProgressIndicator())
                        : DropdownButtonFormField<int>(
                            value: _selectedCourseId,
                            decoration: const InputDecoration(
                              labelText: 'المادة الدراسية *',
                              prefixIcon: Icon(
                                Icons.book_rounded,
                                color: AppColors.primary,
                              ),
                            ),
                            items: _courses.map((c) {
                              return DropdownMenuItem<int>(
                                value: c['id'],
                                child: Text(c['name'] ?? ''),
                              );
                            }).toList(),
                            onChanged: (val) {
                              setState(() => _selectedCourseId = val);
                            },
                          ),
                    const SizedBox(height: 16),

                    // Category
                    DropdownButtonFormField<String>(
                      value: _selectedCategory,
                      decoration: const InputDecoration(
                        labelText: 'تصنيف الملف *',
                        prefixIcon: Icon(
                          Icons.category_rounded,
                          color: AppColors.primary,
                        ),
                      ),
                      items: _categories.entries.map((e) {
                        return DropdownMenuItem<String>(
                          value: e.key,
                          child: Text(e.value),
                        );
                      }).toList(),
                      onChanged: (val) {
                        if (val != null)
                          setState(() => _selectedCategory = val);
                      },
                    ),
                    const SizedBox(height: 16),

                    // File Type
                    DropdownButtonFormField<String>(
                      value: _selectedFileType,
                      decoration: const InputDecoration(
                        labelText: 'نوع المحتوى الأكاديمي',
                        prefixIcon: Icon(
                          Icons.insert_drive_file_rounded,
                          color: AppColors.primary,
                        ),
                      ),
                      items: _fileTypes.entries.map((e) {
                        return DropdownMenuItem<String>(
                          value: e.key,
                          child: Text(e.value),
                        );
                      }).toList(),
                      onChanged: (val) {
                        if (val != null)
                          setState(() => _selectedFileType = val);
                      },
                    ),
                    const SizedBox(height: 16),

                    // Description
                    TextFormField(
                      controller: _descriptionController,
                      maxLines: 3,
                      decoration: const InputDecoration(
                        labelText: 'ملاحظات وتفاصيل إضافية (اختياري)',
                        hintText:
                            'اكتب وصف مختصر لمحتوى الملخص أو الأجزاء المغطاة...',
                        prefixIcon: Icon(
                          Icons.notes_rounded,
                          color: AppColors.primary,
                        ),
                      ),
                    ),
                    const SizedBox(height: 28),

                    // Submit Button
                    ElevatedButton.icon(
                      onPressed: _isUploading ? null : _handleUpload,
                      icon: _isUploading
                          ? const SizedBox(
                              width: 20,
                              height: 20,
                              child: CircularProgressIndicator(
                                color: Colors.white,
                                strokeWidth: 2,
                              ),
                            )
                          : const Icon(Icons.cloud_upload_rounded),
                      label: Text(
                        _isUploading
                            ? 'جاري رفع الملف...'
                            : 'رفع ونشر الملف للطلاب',
                      ),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.primary,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(14),
                        ),
                        textStyle: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildAccessDeniedWidget(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.amber.withValues(alpha: 0.15),
                shape: BoxShape.circle,
              ),
              child: const Icon(
                Icons.lock_person_rounded,
                size: 64,
                color: Colors.amber,
              ),
            ),
            const SizedBox(height: 20),
            const Text(
              'خاص بالمندوبين والمشرفين',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: AppColors.primary,
              ),
            ),
            const SizedBox(height: 12),
            const Text(
              'عذراً، إمكانية رفع الملخصات والمحاضرات مخصصة فقط لمندوبي الدفعات والمشرفين الأكاديميين.\n\nيمكنك كطالب الاطلاع على كافة الملخصات والنماذج والمحاضرات وتحميلها مباشرة.',
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 14,
                color: Colors.black87,
                height: 1.5,
              ),
            ),
            const SizedBox(height: 28),
            ElevatedButton.icon(
              onPressed: () {
                Navigator.pop(context);
                Navigator.pushNamed(context, '/request-role');
              },
              icon: const Icon(Icons.verified_user_rounded),
              label: const Text('تقديم طلب ترقية كمندوب دفعة'),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.primary,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(
                  horizontal: 24,
                  vertical: 14,
                ),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
