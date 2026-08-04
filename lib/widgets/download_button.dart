import 'package:flutter/material.dart';
import '../services/download_manager.dart';
import '../services/download_service.dart';
import '../core/constants/app_colors.dart';

/// زر تحميل ذكي يعرض حالة التحميل مباشرة (بدون حجب الشاشة)
class DownloadButton extends StatefulWidget {
  final int fileId;
  final String fileTitle;
  final String? rawFilePath;

  const DownloadButton({
    super.key,
    required this.fileId,
    required this.fileTitle,
    this.rawFilePath,
  });

  @override
  State<DownloadButton> createState() => _DownloadButtonState();
}

class _DownloadButtonState extends State<DownloadButton>
    with SingleTickerProviderStateMixin {
  late final AnimationController _pulseController;
  final _manager = DownloadManager();

  @override
  void initState() {
    super.initState();
    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 900),
    );
    _manager.addListener(_onStateChanged);
  }

  @override
  void dispose() {
    _manager.removeListener(_onStateChanged);
    _pulseController.dispose();
    super.dispose();
  }

  void _onStateChanged() {
    if (!mounted) return;
    final state = _manager.getState(widget.fileId);
    if (state == DownloadState.downloading) {
      _pulseController.repeat(reverse: true);
    } else {
      _pulseController.stop();
      _pulseController.reset();
    }
    setState(() {});
  }

  void _startDownload() {
    if (_manager.isDownloading(widget.fileId)) return;
    DownloadService.downloadFileBackground(
      context,
      fileId: widget.fileId,
      fileTitle: widget.fileTitle,
      rawFilePath: widget.rawFilePath,
    );
  }

  @override
  Widget build(BuildContext context) {
    final state = _manager.getState(widget.fileId);

    if (state == DownloadState.downloading) {
      return SizedBox(
        width: 40,
        height: 40,
        child: Stack(
          alignment: Alignment.center,
          children: [
            const SizedBox(
              width: 28,
              height: 28,
              child: CircularProgressIndicator(
                strokeWidth: 2.5,
                color: AppColors.primary,
              ),
            ),
            FadeTransition(
              opacity: _pulseController.drive(Tween(begin: 0.4, end: 1.0)),
              child: const Icon(Icons.download_rounded, color: AppColors.primary, size: 16),
            ),
          ],
        ),
      );
    }

    if (state == DownloadState.done) {
      return IconButton(
        icon: const Icon(Icons.check_circle_rounded, color: Colors.green),
        tooltip: 'تم التحميل بنجاح',
        onPressed: () {},
      );
    }

    if (state == DownloadState.error) {
      return IconButton(
        icon: const Icon(Icons.error_outline_rounded, color: Colors.red),
        tooltip: 'فشل التحميل، اضغط للمحاولة مجدداً',
        onPressed: _startDownload,
      );
    }

    // الحالة العادية
    return IconButton(
      icon: const Icon(Icons.download_rounded, color: AppColors.primary),
      tooltip: 'تحميل الملف',
      onPressed: _startDownload,
    );
  }
}
