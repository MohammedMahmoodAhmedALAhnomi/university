import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../core/constants/app_colors.dart';
import '../providers/auth_provider.dart';
import '../providers/theme_provider.dart';
import '../screens/auth/login_screen.dart';
import '../screens/profile/request_role_screen.dart';
import '../screens/about/about_screen.dart';
import '../screens/contact/contact_screen.dart';
import '../screens/admin/admin_dashboard_screen.dart';
import '../screens/notifications/notifications_screen.dart';
import '../screens/bookmarks/bookmarks_screen.dart';




class AppDrawer extends StatelessWidget {
  final Function(int)? onSelectTab;

  const AppDrawer({super.key, this.onSelectTab});

  @override
  Widget build(BuildContext context) {
    final auth = Provider.of<AuthProvider>(context);
    final theme = Provider.of<ThemeProvider>(context);
    final user = auth.user;
    final isDark = theme.isDark;

    return Drawer(
      child: Column(
        children: [
          // Ultra Luxury Header
          Container(
            width: double.infinity,
            padding: const EdgeInsets.only(top: 54, bottom: 24, left: 20, right: 20),
            decoration: const BoxDecoration(
              gradient: AppColors.heroGradient,
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(3),
                      decoration: const BoxDecoration(
                        gradient: AppColors.goldGradient,
                        shape: BoxShape.circle,
                      ),
                      child: CircleAvatar(
                        radius: 30,
                        backgroundColor: isDark ? AppColors.surfaceDark : Colors.white,
                        child: CircleAvatar(
                          radius: 27,
                          backgroundColor: AppColors.primary.withValues(alpha: 0.15),
                          child: Text(
                            auth.isAuthenticated && user != null && user.fullName.isNotEmpty
                                ? user.fullName[0].toUpperCase()
                                : 'U',
                            style: const TextStyle(
                              fontSize: 24,
                              fontWeight: FontWeight.bold,
                              color: AppColors.primary,
                            ),
                          ),
                        ),
                      ),
                    ),
                    Container(
                      decoration: BoxDecoration(
                        color: Colors.white.withValues(alpha: 0.15),
                        shape: BoxShape.circle,
                      ),
                      child: IconButton(
                        icon: Icon(
                          isDark ? Icons.light_mode_rounded : Icons.dark_mode_rounded,
                          color: Colors.amber,
                        ),
                        onPressed: () => theme.toggleTheme(),
                        tooltip: 'تغيير المظهر',
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 14),
                Text(
                  auth.isAuthenticated && user != null ? user.fullName : 'أهلاً بك في البوابة الأكاديمية',
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  auth.isAuthenticated && user != null ? user.email : 'سجل الدخول لتجربة متكاملة',
                  style: TextStyle(
                    color: Colors.white.withValues(alpha: 0.85),
                    fontSize: 12,
                  ),
                ),
                if (auth.isAuthenticated && user != null) ...[
                  const SizedBox(height: 10),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.white.withValues(alpha: 0.2),
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: Colors.white.withValues(alpha: 0.3)),
                    ),
                    child: Text(
                      _getRoleArabic(user.role),
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ],
              ],
            ),
          ),

          // Drawer Body Links
          Expanded(
            child: ListView(
              padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 12),
              children: [
                _buildDrawerTile(
                  context,
                  icon: Icons.home_rounded,
                  title: 'الرئيسية',
                  color: AppColors.primary,
                  onTap: () {
                    Navigator.pop(context);
                    if (onSelectTab != null) onSelectTab!(0);
                  },
                ),
                _buildDrawerTile(
                  context,
                  icon: Icons.menu_book_rounded,
                  title: 'التخصصات والأقسام',
                  color: AppColors.secondary,
                  onTap: () {
                    Navigator.pop(context);
                    if (onSelectTab != null) onSelectTab!(1);
                  },
                ),
                _buildDrawerTile(
                  context,
                  icon: Icons.campaign_rounded,
                  title: 'الإعلانات والأنشطة',
                  color: AppColors.accentRose,
                  onTap: () {
                    Navigator.pop(context);
                    if (onSelectTab != null) onSelectTab!(2);
                  },
                ),
                _buildDrawerTile(
                  context,
                  icon: Icons.notifications_active_rounded,
                  title: 'التنبيهات والإشعارات',
                  color: AppColors.accentAmber,
                  onTap: () {
                    Navigator.pop(context);
                    Navigator.push(context, _createPageRoute(const NotificationsScreen()));
                  },
                ),
                _buildDrawerTile(
                  context,
                  icon: Icons.bookmark_border_rounded,
                  title: 'المفضلة والمحفوظات',
                  color: AppColors.primary,
                  onTap: () {
                    Navigator.pop(context);
                    Navigator.push(context, _createPageRoute(const BookmarksScreen()));
                  },
                ),


                const Padding(
                  padding: EdgeInsets.symmetric(vertical: 8),
                  child: Divider(indent: 12, endIndent: 12),
                ),
                if (auth.isAuthenticated && user != null && (user.role == 'admin' || user.role == 'major_admin' || user.role == 'manager')) ...[
                  _buildDrawerTile(
                    context,
                    icon: Icons.admin_panel_settings_rounded,
                    title: 'لوحة الإدارة والتحكم للمندوبين',
                    color: AppColors.primary,
                    onTap: () {
                      Navigator.pop(context);
                      Navigator.push(context, _createPageRoute(const AdminDashboardScreen()));
                    },
                  ),
                  const Padding(
                    padding: EdgeInsets.symmetric(vertical: 8),
                    child: Divider(indent: 12, endIndent: 12),
                  ),
                ] else ...[
                  _buildDrawerTile(
                    context,
                    icon: Icons.badge_outlined,
                    title: 'طلب ترقية (مندوب دفعة)',
                    color: AppColors.accentAmber,
                    onTap: () {
                      Navigator.pop(context);
                      Navigator.push(context, _createPageRoute(const RequestRoleScreen()));
                    },
                  ),
                  const Padding(
                    padding: EdgeInsets.symmetric(vertical: 8),
                    child: Divider(indent: 12, endIndent: 12),
                  ),
                ],

                _buildDrawerTile(
                  context,
                  icon: Icons.info_outline_rounded,
                  title: 'من نحن',
                  color: AppColors.accentBlue,

                  onTap: () {
                    Navigator.pop(context);
                    Navigator.push(context, _createPageRoute(const AboutScreen()));
                  },
                ),
                _buildDrawerTile(
                  context,
                  icon: Icons.headset_mic_rounded,
                  title: 'اتصل بنا والتعليمات',
                  color: AppColors.accentEmerald,
                  onTap: () {
                    Navigator.pop(context);
                    Navigator.push(context, _createPageRoute(const ContactScreen()));
                  },
                ),
              ],
            ),
          ),

          // Footer Action Container
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              border: Border(
                top: BorderSide(
                  color: isDark ? Colors.white.withValues(alpha: 0.1) : Colors.grey.withValues(alpha: 0.15),
                ),
              ),
            ),
            child: auth.isAuthenticated
                ? InkWell(
                    onTap: () async {
                      Navigator.pop(context);
                      await auth.logout();
                      if (context.mounted) {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('تم تسجيل الخروج بنجاح')),
                        );
                      }
                    },
                    borderRadius: BorderRadius.circular(12),
                    child: Container(
                      padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
                      decoration: BoxDecoration(
                        color: AppColors.error.withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: const Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.logout_rounded, color: AppColors.error, size: 20),
                          SizedBox(width: 8),
                          Text(
                            'تسجيل الخروج',
                            style: TextStyle(color: AppColors.error, fontWeight: FontWeight.bold),
                          ),
                        ],
                      ),
                    ),
                  )
                : Container(
                    decoration: BoxDecoration(
                      gradient: AppColors.primaryGradient,
                      borderRadius: BorderRadius.circular(14),
                      boxShadow: [
                        BoxShadow(
                          color: AppColors.primary.withValues(alpha: 0.3),
                          blurRadius: 10,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: ElevatedButton.icon(
                      onPressed: () {
                        Navigator.pop(context);
                        Navigator.push(context, _createPageRoute(const LoginScreen()));
                      },
                      icon: const Icon(Icons.login_rounded, color: Colors.white),
                      label: const Text(
                        'تسجيل الدخول / حساب جديد',
                        style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                      ),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.transparent,
                        shadowColor: Colors.transparent,
                        minimumSize: const Size(double.infinity, 48),
                      ),
                    ),
                  ),
          ),
        ],
      ),
    );
  }

  static Widget _buildDrawerTile(
    BuildContext context, {
    required IconData icon,
    required String title,
    required VoidCallback onTap,
    Color? color,
  }) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final itemColor = color ?? AppColors.primary;

    return Container(
      margin: const EdgeInsets.only(bottom: 6),
      child: ListTile(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        leading: Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: itemColor.withValues(alpha: 0.12),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, color: itemColor, size: 20),
        ),
        title: Text(
          title,
          style: TextStyle(
            fontWeight: FontWeight.bold,
            fontSize: 14,
            color: isDark ? AppColors.textPrimaryDark : AppColors.textPrimaryLight,
          ),
        ),
        trailing: const Icon(Icons.arrow_forward_ios_rounded, size: 14, color: Colors.grey),
        onTap: onTap,
      ),
    );
  }

  static String _getRoleArabic(String role) {
    switch (role) {
      case 'admin':
        return 'مدير النظام الشامل';
      case 'major_admin':
        return 'رئيس التخصص الأكاديمي';
      case 'manager':
        return 'مندوب / مدير مستوى';
      default:
        return 'طالب / زائر';
    }
  }

  static Route _createPageRoute(Widget page) {
    return PageRouteBuilder(
      pageBuilder: (context, animation, secondaryAnimation) => page,
      transitionsBuilder: (context, animation, secondaryAnimation, child) {
        const begin = Offset(1.0, 0.0);
        const end = Offset.zero;
        const curve = Curves.easeInOutCubic;
        var tween = Tween(begin: begin, end: end).chain(CurveTween(curve: curve));
        var offsetAnimation = animation.drive(tween);
        return SlideTransition(
          position: offsetAnimation,
          child: FadeTransition(opacity: animation, child: child),
        );
      },
    );
  }
}
