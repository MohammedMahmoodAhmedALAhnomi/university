import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/university_provider.dart';
import '../utils/constants.dart';
import '../widgets/announcement_card.dart';
import '../widgets/custom_drawer.dart';
import '../widgets/file_tile.dart';
import '../widgets/major_card.dart';
import 'announcements_screen.dart';
import 'majors_screen.dart';
import 'search_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<UniversityProvider>(context, listen: false).fetchHomeData();
    });
  }

  Widget _buildStatChip(String title, String count, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: color.withOpacity(0.2)),
      ),
      child: Column(
        children: [
          Icon(icon, color: color, size: 24),
          const SizedBox(height: 6),
          Text(
            count,
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            title,
            style: TextStyle(
              fontSize: 12,
              color: color.withOpacity(0.8),
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final uniProvider = Provider.of<UniversityProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text(AppConstants.appName),
        actions: [
          IconButton(
            icon: const Icon(Icons.search),
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const SearchScreen()),
              );
            },
          ),
        ],
      ),
      drawer: const CustomDrawer(),
      body: RefreshIndicator(
        onRefresh: () async {
          await uniProvider.fetchHomeData();
        },
        child: uniProvider.isHomeLoading
            ? const Center(child: CircularProgressIndicator())
            : SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                physics: const AlwaysScrollableScrollPhysics(),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Welcome Banner
                    Container(
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [AppConstants.primaryColor, AppConstants.primaryDarkColor],
                          begin: Alignment.topRight,
                          end: Alignment.bottomLeft,
                        ),
                        borderRadius: BorderRadius.circular(20),
                        boxShadow: [
                          BoxShadow(
                            color: AppConstants.primaryColor.withOpacity(0.3),
                            blurRadius: 10,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Row(
                            children: [
                              Icon(Icons.auto_awesome, color: AppConstants.goldAccent, size: 24),
                              SizedBox(width: 8),
                              Text(
                                'أهلاً بك في البوابة التعليمية',
                                style: TextStyle(
                                  color: Colors.white,
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 8),
                          Text(
                            'المصدر الموحد لجميع المحاضرات، الملخصات ونماذج الاختبارات الأكاديمية متزامن لحظياً مع الموقع الرسمى.',
                            style: TextStyle(
                              color: Colors.white.withOpacity(0.9),
                              fontSize: 13,
                              height: 1.5,
                            ),
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 24),

                    // Quick Stats Section
                    Row(
                      children: [
                        Expanded(
                          child: _buildStatChip(
                            'تخصص',
                            '${uniProvider.stats['majors'] ?? 0}',
                            Icons.school_outlined,
                            AppConstants.primaryColor,
                          ),
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: _buildStatChip(
                            'مادة',
                            '${uniProvider.stats['courses'] ?? 0}',
                            Icons.menu_book_outlined,
                            Colors.purple.shade600,
                          ),
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: _buildStatChip(
                            'ملف',
                            '${uniProvider.stats['files'] ?? 0}',
                            Icons.folder_copy_outlined,
                            AppConstants.accentColor,
                          ),
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: _buildStatChip(
                            'تحميل',
                            '${uniProvider.stats['downloads'] ?? 0}',
                            Icons.file_download_outlined,
                            AppConstants.goldAccent,
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(height: 28),

                    // Pinned Announcements Section
                    if (uniProvider.pinnedAnnouncements.isNotEmpty) ...[
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Row(
                            children: [
                              Icon(Icons.campaign, color: AppConstants.goldAccent),
                              SizedBox(width: 8),
                              Text(
                                'الإعلانات والتوجهات',
                                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                              ),
                            ],
                          ),
                          TextButton(
                            onPressed: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(builder: (_) => const AnnouncementsScreen()),
                              );
                            },
                            child: const Text('عرض الكل'),
                          ),
                        ],
                      ),
                      const SizedBox(height: 8),
                      ...uniProvider.pinnedAnnouncements.map((a) => AnnouncementCard(announcement: a)),
                      const SizedBox(height: 24),
                    ],

                    // Majors Section Header
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Row(
                          children: [
                            Icon(Icons.account_balance, color: AppConstants.primaryColor),
                            SizedBox(width: 8),
                            Text(
                              'التخصصات الأكاديمية',
                              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                            ),
                          ],
                        ),
                        TextButton(
                          onPressed: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(builder: (_) => const MajorsScreen()),
                            );
                          },
                          child: const Text('عرض الكل'),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),

                    // Majors Grid
                    if (uniProvider.majors.isEmpty)
                      const Center(child: Text('لا توجد تخصصات متاحة حالياً'))
                    else
                      GridView.builder(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: 2,
                          childAspectRatio: 1.2,
                          crossAxisSpacing: 12,
                          mainAxisSpacing: 12,
                        ),
                        itemCount: uniProvider.majors.length > 4 ? 4 : uniProvider.majors.length,
                        itemBuilder: (context, index) {
                          return MajorCard(major: uniProvider.majors[index]);
                        },
                      ),

                    const SizedBox(height: 28),

                    // Recent Uploaded Files Header
                    const Row(
                      children: [
                        Icon(Icons.new_releases_outlined, color: AppConstants.accentColor),
                        SizedBox(width: 8),
                        Text(
                          'أحدث المحاضرات والملفات المرفوعة',
                          style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),

                    if (uniProvider.recentFiles.isEmpty)
                      const Padding(
                        padding: EdgeInsets.all(16),
                        child: Center(child: Text('لا توجد ملفات مرفوعة حديثاً')),
                      )
                    else
                      ListView.builder(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        itemCount: uniProvider.recentFiles.length,
                        itemBuilder: (context, index) {
                          return FileTile(fileItem: uniProvider.recentFiles[index]);
                        },
                      ),
                  ],
                ),
              ),
      ),
    );
  }
}
