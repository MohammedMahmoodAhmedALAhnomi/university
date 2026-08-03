import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import '../providers/theme_provider.dart';
import '../screens/announcements_screen.dart';
import '../screens/home_screen.dart';
import '../screens/login_screen.dart';
import '../screens/majors_screen.dart';
import '../screens/profile_screen.dart';
import '../screens/search_screen.dart';
import '../screens/server_settings_screen.dart';
import '../services/storage_service.dart';
import '../utils/constants.dart';

class CustomDrawer extends StatelessWidget {
  const CustomDrawer({super.key});

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final themeProvider = Provider.of<ThemeProvider>(context);
    final user = authProvider.user;

    return Drawer(
      child: Column(
        children: [
          UserAccountsDrawerHeader(
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                colors: [AppConstants.primaryColor, AppConstants.primaryDarkColor],
                begin: Alignment.topRight,
                end: Alignment.bottomLeft,
              ),
            ),
            accountName: Text(
              user?.fullName ?? AppConstants.appName,
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
            ),
            accountEmail: Text(user?.email ?? AppConstants.appSubTitle),
            currentAccountPicture: CircleAvatar(
              backgroundColor: Colors.white,
              child: Icon(
                user != null ? Icons.person : Icons.school,
                size: 36,
                color: AppConstants.primaryColor,
              ),
            ),
          ),
          ListTile(
            leading: const Icon(Icons.home_outlined),
            title: const Text('الرئيسية'),
            onTap: () {
              Navigator.pushReplacement(
                context,
                MaterialPageRoute(builder: (_) => const HomeScreen()),
              );
            },
          ),
          ListTile(
            leading: const Icon(Icons.school_outlined),
            title: const Text('التخصصات الأكاديمية'),
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const MajorsScreen()),
              );
            },
          ),
          ListTile(
            leading: const Icon(Icons.campaign_outlined),
            title: const Text('الإعلانات والأخبار'),
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const AnnouncementsScreen()),
              );
            },
          ),
          ListTile(
            leading: const Icon(Icons.search_outlined),
            title: const Text('البحث في المحتوى'),
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const SearchScreen()),
              );
            },
          ),
          const Divider(),
          SwitchListTile(
            secondary: Icon(
              themeProvider.isDarkMode ? Icons.dark_mode : Icons.light_mode,
            ),
            title: const Text('الوضع المظلم'),
            value: themeProvider.isDarkMode,
            onChanged: (val) {
              themeProvider.toggleTheme();
            },
          ),
          ListTile(
            leading: const Icon(Icons.dns_outlined),
            title: const Text('إعدادات السيرفر'),
            subtitle: Text(
              StorageService.getBaseUrl(),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(fontSize: 12),
            ),
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const ServerSettingsScreen()),
              );
            },
          ),
          const Spacer(),
          const Divider(),
          if (authProvider.isAuthenticated) ...[
            ListTile(
              leading: const Icon(Icons.person_outline),
              title: const Text('الملف الشخصي'),
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const ProfileScreen()),
                );
              },
            ),
            ListTile(
              leading: const Icon(Icons.logout, color: Colors.red),
              title: const Text('تسجيل الخروج', style: TextStyle(color: Colors.red)),
              onTap: () async {
                await authProvider.logout();
                if (context.mounted) {
                  Navigator.pop(context);
                }
              },
            ),
          ] else ...[
            ListTile(
              leading: const Icon(Icons.login, color: AppConstants.primaryColor),
              title: const Text(
                'تسجيل الدخول / إنشاء حساب',
                style: TextStyle(color: AppConstants.primaryColor, fontWeight: FontWeight.bold),
              ),
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const LoginScreen()),
                );
              },
            ),
          ],
          const SizedBox(height: 16),
        ],
      ),
    );
  }
}
