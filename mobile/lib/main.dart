import 'package:flutter/material.dart';
import 'package:untitled/presentation/screens/splash/splash_screen.dart';

void main() {
  runApp(const NexusApp());
}

class NexusApp extends StatelessWidget {
  const NexusApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'NEXUS',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        primaryColor: const Color(0xFF3C3C3C),
        scaffoldBackgroundColor: const Color(0xFFF5F5F5),
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFFFF6B6B),
          primary: const Color(0xFFFF6B6B),
        ),
        useMaterial3: true,
        fontFamily: 'Roboto',
      ),
      //Démarre par le splash puis redirige vers AuthScreen ou HomeScreen selon le statut de connexion
      home: const SplashScreen(),
    );
  }
}
