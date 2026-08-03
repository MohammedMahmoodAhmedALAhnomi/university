import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/university_provider.dart';
import '../services/storage_service.dart';
import '../utils/constants.dart';

class ServerSettingsScreen extends StatefulWidget {
  const ServerSettingsScreen({super.key});

  @override
  State<ServerSettingsScreen> createState() => _ServerSettingsScreenState();
}

class _ServerSettingsScreenState extends State<ServerSettingsScreen> {
  final _urlController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _urlController.text = StorageService.getBaseUrl();
  }

  @override
  void dispose() {
    _urlController.dispose();
    super.dispose();
  }

  void _saveUrl() async {
    final newUrl = _urlController.text.trim();
    if (newUrl.isNotEmpty) {
      await StorageService.setBaseUrl(newUrl);
      if (mounted) {
        Provider.of<UniversityProvider>(context, listen: false).fetchHomeData();
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('تم حفظ رابط السيرفر بنجاح!')),
        );
        Navigator.pop(context);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('إعدادات الاتصال بالسيرفر'),
      ),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            const Icon(
              Icons.dns_rounded,
              size: 64,
              color: AppConstants.primaryColor,
            ),
            const SizedBox(height: 16),
            const Text(
              'رابط السيرفر المرفوع اونلاين (Domain URL)',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 8),
            const Text(
              'أدخل رابط الموقع المرفوع عليه النظام ليتصل التطبيق بقاعدة البيانات في الوقت الفعلي:',
              style: TextStyle(fontSize: 13, color: Colors.grey),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 24),
            TextField(
              controller: _urlController,
              decoration: const InputDecoration(
                labelText: 'رابط السيرفر / الدومين',
                hintText: 'https://your-university-domain.com',
                prefixIcon: Icon(Icons.link),
              ),
            ),
            const SizedBox(height: 12),
            const Wrap(
              spacing: 8,
              children: [
                Chip(label: Text('مثال أونلاين: https://university.com')),
                Chip(label: Text('محاكي أندرويد: http://10.0.2.2/university')),
              ],
            ),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: _saveUrl,
              child: const Text('حفظ واختبار الاتصال'),
            ),
          ],
        ),
      ),
    );
  }
}
