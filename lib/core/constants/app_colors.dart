import 'package:flutter/material.dart';

class AppColors {
  // Pure Royal Blue Palette (اللون الأزرق الملكي المباشر والأصيل)
  static const Color primary = Color(0xFF0052CC); // Pure Royal Blue
  static const Color primaryDark = Color(0xFF0A2540); // Deep Royal Navy
  static const Color primaryLight = Color(0xFF2563EB); // Bright Royal Blue

  static const Color secondary = Color(0xFF0284C7); // Sapphire Ocean
  static const Color secondaryDark = Color(0xFF0369A1);
  static const Color secondaryLight = Color(0xFF38BDF8);

  static const Color accentAmber = Color(0xFFFFB703); // Royal Gold
  static const Color accentEmerald = Color(0xFF10B981); // Emerald
  static const Color accentRose = Color(0xFFE11D48); // Classic Red Rose
  static const Color accentBlue = Color(0xFF0284C7); // Sky Blue


  // Light Mode Surfaces & Cards
  static const Color backgroundLight = Color(0xFFF8FAFC);
  static const Color surfaceLight = Colors.white;
  static const Color cardLight = Colors.white;

  // Dark Mode Surfaces & Cards
  static const Color backgroundDark = Color(0xFF0B132B); // Obsidian Royal Navy
  static const Color surfaceDark = Color(0xFF1C2541);
  static const Color cardDark = Color(0xFF1C2541);

  // Gradients (Royal Blue Multi-Stop Gradients)
  static const LinearGradient primaryGradient = LinearGradient(
    colors: [Color(0xFF0052CC), Color(0xFF1D4ED8), Color(0xFF2563EB)],
    begin: Alignment.topRight,
    end: Alignment.bottomLeft,
  );

  static const LinearGradient heroGradient = LinearGradient(
    colors: [Color(0xFF0A2540), Color(0xFF0052CC), Color(0xFF0284C7)],
    begin: Alignment.topRight,
    end: Alignment.bottomLeft,
  );

  static const LinearGradient blueGradient = LinearGradient(
    colors: [Color(0xFF0052CC), Color(0xFF3B82F6)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient goldGradient = LinearGradient(
    colors: [Color(0xFFFFB703), Color(0xFFFBBF24)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient emeraldGradient = LinearGradient(
    colors: [Color(0xFF10B981), Color(0xFF34D399)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  // Text Colors
  static const Color textPrimaryLight = Color(0xFF0F172A);
  static const Color textSecondaryLight = Color(0xFF475569);

  static const Color textPrimaryDark = Color(0xFFF8FAFC);
  static const Color textSecondaryDark = Color(0xFF94A3B8);

  // Helper Status Colors
  static const Color success = Color(0xFF10B981);
  static const Color error = Color(0xFFE11D48);
  static const Color warning = Color(0xFFFFB703);
  static const Color info = Color(0xFF0052CC);
}
