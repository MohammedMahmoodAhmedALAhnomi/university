import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'core/constants/app_colors.dart';
import 'providers/auth_provider.dart';
import 'providers/academic_provider.dart';
import 'providers/notification_provider.dart';
import 'providers/bookmark_provider.dart';
import 'providers/theme_provider.dart';
import 'screens/splash_screen.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const UniversityApp());
}

class UniversityApp extends StatelessWidget {
  const UniversityApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => ThemeProvider()),
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => AcademicProvider()),
        ChangeNotifierProvider(create: (_) => NotificationProvider()),
        ChangeNotifierProvider(create: (_) => BookmarkProvider()),
      ],


      child: Consumer<ThemeProvider>(
        builder: (context, themeProvider, _) {
          return MaterialApp(

            title: 'اللجنة العلمية',
            debugShowCheckedModeBanner: false,
            locale: const Locale('ar', 'SA'),
            supportedLocales: const [
              Locale('ar', 'SA'),
              Locale('en', 'US'),
            ],
            localizationsDelegates: const [
              GlobalMaterialLocalizations.delegate,
              GlobalWidgetsLocalizations.delegate,
              GlobalCupertinoLocalizations.delegate,
            ],
            themeMode: themeProvider.isDark ? ThemeMode.dark : ThemeMode.light,
            theme: ThemeData(
              useMaterial3: true,
              colorScheme: ColorScheme.fromSeed(
                seedColor: AppColors.primary,
                primary: AppColors.primary,
                secondary: AppColors.secondary,
                brightness: Brightness.light,
                surface: AppColors.surfaceLight,
              ),
              scaffoldBackgroundColor: AppColors.backgroundLight,
              textTheme: GoogleFonts.cairoTextTheme(ThemeData.light().textTheme),
              appBarTheme: AppBarTheme(
                backgroundColor: AppColors.surfaceLight,
                surfaceTintColor: Colors.transparent,
                elevation: 0,
                centerTitle: false,
                iconTheme: const IconThemeData(color: AppColors.textPrimaryLight),
                titleTextStyle: GoogleFonts.cairo(
                  color: AppColors.textPrimaryLight,
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              cardTheme: CardThemeData(
                color: AppColors.cardLight,
                elevation: 2,
                shadowColor: Colors.black.withValues(alpha: 0.08),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              ),
              inputDecorationTheme: InputDecorationTheme(
                filled: true,
                fillColor: Colors.grey.withValues(alpha: 0.06),
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                  borderSide: BorderSide.none,
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                  borderSide: BorderSide(color: Colors.grey.withValues(alpha: 0.15)),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                  borderSide: const BorderSide(color: AppColors.primary, width: 2),
                ),
              ),
            ),
            darkTheme: ThemeData(
              useMaterial3: true,
              colorScheme: ColorScheme.fromSeed(
                seedColor: AppColors.primary,
                primary: AppColors.primaryLight,
                secondary: AppColors.secondaryLight,
                brightness: Brightness.dark,
                surface: AppColors.surfaceDark,
              ),
              scaffoldBackgroundColor: AppColors.backgroundDark,
              textTheme: GoogleFonts.cairoTextTheme(ThemeData.dark().textTheme),
              appBarTheme: AppBarTheme(
                backgroundColor: AppColors.surfaceDark,
                surfaceTintColor: Colors.transparent,
                elevation: 0,
                centerTitle: false,
                iconTheme: const IconThemeData(color: AppColors.textPrimaryDark),
                titleTextStyle: GoogleFonts.cairo(
                  color: AppColors.textPrimaryDark,
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              cardTheme: CardThemeData(
                color: AppColors.cardDark,
                elevation: 2,
                shadowColor: Colors.black.withValues(alpha: 0.3),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              ),
              inputDecorationTheme: InputDecorationTheme(
                filled: true,
                fillColor: Colors.white.withValues(alpha: 0.05),
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                  borderSide: BorderSide.none,
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                  borderSide: BorderSide(color: Colors.white.withValues(alpha: 0.1)),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                  borderSide: const BorderSide(color: AppColors.primaryLight, width: 2),
                ),
              ),
            ),
            home: const SplashScreen(),
          );
        },
      ),
    );
  }
}
