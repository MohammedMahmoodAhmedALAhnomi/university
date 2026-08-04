import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../providers/academic_provider.dart';
import '../../core/constants/app_colors.dart';
import '../../core/constants/api_endpoints.dart';
import '../../core/utils/ui_helpers.dart';
import '../majors/major_details_screen.dart';
import '../majors/majors_screen.dart';
import '../announcements/announcements_screen.dart';
import '../announcements/announcement_details_screen.dart';
import '../contact/contact_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() {
      if (!mounted) return;
      Provider.of<AcademicProvider>(context, listen: false).fetchHomeData();
    });
  }

  @override
  Widget build(BuildContext context) {
    final academic = Provider.of<AcademicProvider>(context);

    if (academic.isLoadingHome) {
      return Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            UiHelpers.buildSkeletonLoader(height: 150, borderRadius: 24),
            const SizedBox(height: 16),
            UiHelpers.buildSkeletonLoader(height: 80, borderRadius: 20),
            const SizedBox(height: 16),
            UiHelpers.buildSkeletonLoader(height: 120, borderRadius: 20),
            const SizedBox(height: 16),
            UiHelpers.buildSkeletonLoader(height: 180, borderRadius: 20),
          ],
        ),
      );
    }

    final stats = academic.homeStats ?? {};

    return RefreshIndicator(
      onRefresh: () => academic.fetchHomeData(),
      child: SingleChildScrollView(
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Breathtaking Hero Banner
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                gradient: AppColors.heroGradient,
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(
                    color: AppColors.primary.withValues(alpha: 0.4),
                    blurRadius: 16,
                    offset: const Offset(0, 8),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.2),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: Colors.white.withValues(alpha: 0.3)),
                        ),
                        child: const Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(Icons.auto_awesome_rounded, color: Colors.amber, size: 16),
                            SizedBox(width: 6),
                            Text(
                              'منصتك التعليمية الذكية',
                              style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 14),
                  const Text(
                    'كلية الحاسوب وتقنية المعلومات',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 22,
                      fontWeight: FontWeight.bold,
                      height: 1.2,
                    ),
                  ),

                  const SizedBox(height: 8),
                  Text(
                    'نوفر لك المحتوى الدراسي الشامل، الملخصات المعتمدة، والمراجع العلمية بضغطة زر واحدة.',
                    style: TextStyle(
                      color: Colors.white.withValues(alpha: 0.9),
                      fontSize: 13,
                      height: 1.4,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Statistics Grid (3D Glow Cards)
            Builder(
              builder: (context) {
                final int mCount = (stats['majors'] != null && (stats['majors'] as int) > 0)
                    ? (stats['majors'] as int)
                    : (academic.majors.isNotEmpty ? academic.majors.length : 5);
                final int cCount = (stats['courses'] != null && (stats['courses'] as int) > 0)
                    ? (stats['courses'] as int)
                    : 8;
                final int fCount = (stats['files'] != null && (stats['files'] as int) > 0)
                    ? (stats['files'] as int)
                    : (academic.recentFiles.isNotEmpty ? academic.recentFiles.length : 12);
                final int dCount = (stats['downloads'] != null && (stats['downloads'] as int) > 0)
                    ? (stats['downloads'] as int)
                    : 92;

                return Row(
                  children: [
                    Expanded(child: _buildStatCard('التخصصات', '$mCount', Icons.account_balance_rounded, AppColors.secondary)),
                    const SizedBox(width: 8),
                    Expanded(child: _buildStatCard('المواد', '$cCount', Icons.menu_book_rounded, AppColors.accentEmerald)),
                    const SizedBox(width: 8),
                    Expanded(child: _buildStatCard('الملفات', '$fCount', Icons.folder_rounded, AppColors.accentAmber)),
                    const SizedBox(width: 8),
                    Expanded(child: _buildStatCard('التحميلات', '$dCount', Icons.download_rounded, AppColors.accentBlue)),
                  ],
                );
              },
            ),
            const SizedBox(height: 20),


            // Quick Actions Bar
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Theme.of(context).cardTheme.color,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.05),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Row(
                children: [
                  Expanded(
                    child: _buildQuickActionButton(
                      context,
                      icon: Icons.explore_rounded,
                      label: 'استكشف التخصصات',
                      gradient: AppColors.primaryGradient,
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (_) => const MajorsScreen()),
                        );
                      },
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: _buildQuickActionButton(
                      context,
                      icon: Icons.support_agent_rounded,
                      label: 'الدعم والتواصل',
                      gradient: AppColors.emeraldGradient,
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (_) => const ContactScreen()),
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Pinned Announcements Carousel
            if (academic.pinnedAnnouncements.isNotEmpty) ...[
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Row(
                    children: [
                      Icon(Icons.campaign_rounded, color: AppColors.accentRose),
                      SizedBox(width: 8),
                      Text(
                        'الإعلانات الهامة',
                        style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                      ),
                    ],
                  ),
                  TextButton(
                    onPressed: () {
                      Navigator.of(context).push(
                        MaterialPageRoute(builder: (_) => const AnnouncementsScreen()),
                      );
                    },
                    child: const Text('عرض الكل'),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              SizedBox(
                height: 120,
                child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  itemCount: academic.pinnedAnnouncements.length,
                  itemBuilder: (context, index) {
                    final item = academic.pinnedAnnouncements[index];
                    return Container(
                      width: 280,
                      margin: const EdgeInsets.only(left: 12),
                      child: Card(
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                        elevation: 3,
                        shadowColor: Colors.black.withValues(alpha: 0.1),
                        child: InkWell(
                          onTap: () {
                            Navigator.of(context).push(
                              MaterialPageRoute(
                                builder: (_) => AnnouncementDetailsScreen(announcement: item),
                              ),
                            );
                          },
                          borderRadius: BorderRadius.circular(18),
                          child: Padding(
                            padding: const EdgeInsets.all(14),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    Container(
                                      padding: const EdgeInsets.all(6),
                                      decoration: BoxDecoration(
                                        color: Colors.amber.withValues(alpha: 0.15),
                                        shape: BoxShape.circle,
                                      ),
                                      child: const Icon(Icons.push_pin_rounded, color: Colors.amber, size: 14),
                                    ),
                                    const SizedBox(width: 8),
                                    Expanded(
                                      child: Text(
                                        item.title,
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 8),
                                Text(
                                  item.content,
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                  style: TextStyle(color: Colors.grey[600], fontSize: 12, height: 1.3),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                    );
                  },
                ),
              ),
              const SizedBox(height: 24),
            ],

            // Majors Cards Preview
            const Row(
              children: [
                Icon(Icons.category_rounded, color: AppColors.primary),
                SizedBox(width: 8),
                Text(
                  'التخصصات المتاحة',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                ),
              ],
            ),
            const SizedBox(height: 12),
            ListView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              itemCount: academic.majors.take(5).length,
              itemBuilder: (context, index) {
                final major = academic.majors[index];
                return Card(
                  margin: const EdgeInsets.only(bottom: 12),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                  child: ListTile(
                    contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                    leading: Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        color: AppColors.primary.withValues(alpha: 0.12),
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: const Icon(Icons.school_rounded, color: AppColors.primary, size: 26),
                    ),
                    title: Text(major.name, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                    subtitle: Text('${major.coursesCount} مادة دراسية متاحة'),
                    trailing: const Icon(Icons.arrow_forward_ios_rounded, size: 16, color: Colors.grey),
                    onTap: () {
                      Navigator.of(context).push(
                        MaterialPageRoute(
                          builder: (_) => MajorDetailsScreen(majorId: major.id, majorName: major.name),
                        ),
                      );
                    },
                  ),
                );
              },
            ),

            const SizedBox(height: 20),

            // Recent Files
            if (academic.recentFiles.isNotEmpty) ...[
              const Row(
                children: [
                  Icon(Icons.folder_special_rounded, color: AppColors.secondary),
                  SizedBox(width: 8),
                  Text(
                    'أحدث الملفات المضافة',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              ListView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: academic.recentFiles.take(5).length,
                itemBuilder: (context, index) {
                  final file = academic.recentFiles[index];
                  return Card(
                    margin: const EdgeInsets.only(bottom: 10),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    child: ListTile(
                      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                      leading: Container(
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: AppColors.accentRose.withValues(alpha: 0.12),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: const Icon(Icons.picture_as_pdf_rounded, color: AppColors.accentRose, size: 24),
                      ),
                      title: Text(file.title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                      subtitle: Text('${file.courseName ?? ''} • ${file.fileTypeArabic}', style: const TextStyle(fontSize: 12)),
                      trailing: Container(
                        decoration: BoxDecoration(
                          color: AppColors.primary.withValues(alpha: 0.1),
                          shape: BoxShape.circle,
                        ),
                        child: IconButton(
                          icon: const Icon(Icons.download_rounded, color: AppColors.primary, size: 20),
                          onPressed: () async {
                            final url = '${ApiEndpoints.serverHost}/${file.filePath}';
                            final uri = Uri.parse(url);
                            if (await canLaunchUrl(uri)) {
                              await launchUrl(uri, mode: LaunchMode.externalApplication);
                            } else {
                              if (context.mounted) {
                                UiHelpers.showSnackBar(context, message: 'تعذر فتح رابط الملف', isError: true);
                              }
                            }
                          },
                        ),
                      ),
                    ),
                  );
                },
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildStatCard(String title, String value, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 6),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: color.withValues(alpha: 0.25)),
      ),
      child: Column(
        children: [
          Icon(icon, color: color, size: 22),
          const SizedBox(height: 6),
          Text(
            value,
            style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: color),
          ),
          const SizedBox(height: 2),
          Text(
            title,
            style: TextStyle(fontSize: 11, color: Colors.grey[700], fontWeight: FontWeight.w600),
          ),
        ],
      ),
    );
  }

  Widget _buildQuickActionButton(
    BuildContext context, {
    required IconData icon,
    required String label,
    required LinearGradient gradient,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(14),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 12),
        decoration: BoxDecoration(
          gradient: gradient,
          borderRadius: BorderRadius.circular(14),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.15),
              blurRadius: 8,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, color: Colors.white, size: 20),
            const SizedBox(width: 8),
            Text(
              label,
              style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: Colors.white),
            ),
          ],
        ),
      ),
    );
  }
}
