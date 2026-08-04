import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../core/constants/app_colors.dart';

class ContactScreen extends StatelessWidget {
  const ContactScreen({super.key});

  Future<void> _launchUrl(String url) async {
    final uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('اتصل بنا'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            // Header
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(28),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [AppColors.primary, AppColors.secondary],
                ),
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: AppColors.primary.withValues(alpha: 0.3),
                    blurRadius: 12,
                    offset: const Offset(0, 6),
                  ),
                ],
              ),
              child: const Column(
                children: [
                  Icon(Icons.headset_mic_rounded, color: Colors.white, size: 56),
                  SizedBox(height: 12),
                  Text(
                    'اتصل بنا',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  SizedBox(height: 8),
                  Text(
                    'نحن هنا لمساعدتك! يمكنك التواصل المباشر مع مطوري المنصة لأي استفسار أو اقتراح.',
                    style: TextStyle(color: Colors.white70, fontSize: 13, height: 1.4),
                    textAlign: TextAlign.center,
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Developer 1 Card
            _buildDeveloperCard(
              name: 'محمد محمود الأهنومي',
              role: 'المطور الأول للمنصة',
              phone: '771135357',
              whatsapp: '967771135357',
              email: 'mohammedalahnomi04@gmail.com',
              avatarIcon: Icons.code_rounded,
              color: AppColors.primary,
            ),
            const SizedBox(height: 16),

            // Developer 2 Card
            _buildDeveloperCard(
              name: 'سيد احمد حسين الغيلي',
              role: 'المطور الثاني للمنصة',
              phone: '772348925',
              whatsapp: '967772348925',
              email: 'sayedahmed77169@gmail.com',
              avatarIcon: Icons.terminal_rounded,
              color: AppColors.secondary,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDeveloperCard({
    required String name,
    required String role,
    required String phone,
    required String whatsapp,
    required String email,
    required IconData avatarIcon,
    required Color color,
  }) {
    return Card(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      elevation: 3,
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: color.withValues(alpha: 0.12),
                shape: BoxShape.circle,
              ),
              child: Icon(avatarIcon, color: color, size: 32),
            ),
            const SizedBox(height: 10),
            Text(
              name,
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 4),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
              decoration: BoxDecoration(
                color: color.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text(
                role,
                style: TextStyle(color: color, fontWeight: FontWeight.bold, fontSize: 12),
              ),
            ),
            const SizedBox(height: 18),

            // Contact Buttons
            _buildContactButton(
              icon: Icons.phone_rounded,
              label: 'الهاتف',
              value: phone,
              color: color,
              onTap: () => _launchUrl('tel:$phone'),
            ),
            const SizedBox(height: 8),
            _buildContactButton(
              icon: Icons.chat_rounded,
              label: 'واتساب',
              value: phone,
              color: Colors.green,
              onTap: () => _launchUrl('https://wa.me/$whatsapp'),
            ),
            const SizedBox(height: 8),
            _buildContactButton(
              icon: Icons.email_rounded,
              label: 'البريد',
              value: email,
              color: Colors.grey[700]!,
              onTap: () => _launchUrl('mailto:$email'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildContactButton({
    required IconData icon,
    required String label,
    required String value,
    required Color color,
    required VoidCallback onTap,
  }) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
          decoration: BoxDecoration(
            border: Border.all(color: color.withValues(alpha: 0.3)),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Row(
            children: [
              Icon(icon, color: color, size: 20),
              const SizedBox(width: 10),
              Text(label, style: TextStyle(color: color, fontWeight: FontWeight.bold, fontSize: 13)),
              const Spacer(),
              Flexible(
                child: Text(
                  value,
                  style: TextStyle(color: Colors.grey[700], fontSize: 12),
                  textDirection: TextDirection.ltr,
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
