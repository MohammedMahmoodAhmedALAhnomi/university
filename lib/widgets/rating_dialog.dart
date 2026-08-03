import 'package:flutter/material.dart';
import 'package:flutter_rating_bar/flutter_rating_bar.dart';
import 'package:provider/provider.dart';
import '../providers/university_provider.dart';
import '../utils/constants.dart';

class RatingDialog extends StatefulWidget {
  final int courseId;
  final String courseName;

  const RatingDialog({
    super.key,
    required this.courseId,
    required this.courseName,
  });

  @override
  State<RatingDialog> createState() => _RatingDialogState();
}

class _RatingDialogState extends State<RatingDialog> {
  double _userRating = 5.0;
  bool _isSubmitting = false;

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      title: Column(
        children: [
          const Icon(Icons.star_rounded, size: 48, color: AppConstants.goldAccent),
          const SizedBox(height: 8),
          const Text(
            'تقييم المادة الدراسية',
            style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 4),
          Text(
            widget.courseName,
            style: TextStyle(fontSize: 14, color: Colors.grey.shade600),
            textAlign: TextAlign.center,
          ),
        ],
      ),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          const Text(
            'اختر عدد النجوم لتقييم صعوبة أو فائدة المحتوى التعليمي للمادة:',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 13),
          ),
          const SizedBox(height: 16),
          RatingBar.builder(
            initialRating: 5,
            minRating: 1,
            direction: Axis.horizontal,
            allowHalfRating: false,
            itemCount: 5,
            itemPadding: const EdgeInsets.symmetric(horizontal: 4.0),
            itemBuilder: (context, _) => const Icon(
              Icons.star,
              color: AppConstants.goldAccent,
            ),
            onRatingUpdate: (rating) {
              setState(() {
                _userRating = rating;
              });
            },
          ),
          const SizedBox(height: 8),
          Text(
            '${_userRating.toInt()} من 5 نجوم',
            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
          ),
        ],
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(context),
          child: const Text('إلغاء'),
        ),
        ElevatedButton(
          onPressed: _isSubmitting
              ? null
              : () async {
                  setState(() {
                    _isSubmitting = true;
                  });
                  final provider = Provider.of<UniversityProvider>(context, listen: false);
                  final success = await provider.rateCourse(widget.courseId, _userRating.toInt());
                  if (context.mounted) {
                    Navigator.pop(context);
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(
                        content: Text(
                          success ? 'تم حفظ التقييم بنجاح، شكراً لك!' : 'تعذر تسجيل التقييم',
                        ),
                      ),
                    );
                  }
                },
          child: _isSubmitting
              ? const SizedBox(
                  width: 18,
                  height: 18,
                  child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                )
              : const Text('إرسال التقييم'),
        ),
      ],
    );
  }
}
