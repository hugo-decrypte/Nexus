import 'package:flutter/material.dart';
import 'package:untitled/presentation/widgets/base/nexus_app_bar.dart';

import '../../widgets/base/nexus_bottom_nav_bar.dart';
import '../historique/historique_screen.dart';
import '../home/home_screen.dart';

class RechargeScreen extends StatelessWidget {
  const RechargeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      // bar du haut
      appBar: const NexusAppBar(),
      body: const Center(
        child: Text(
          'Rechargement par carte physique',
          style: TextStyle(fontSize: 18, color: Colors.black54),
        ),
      ),
      bottomNavigationBar: NexusBottomNavBar(
        currentIndex: 1,
        onTap: (index) {
          if (index == 1) return;

          if (index == 0) {
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(builder: (_) => const HomeScreen()),
            );
          }

          if (index == 2) {
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(builder: (_) => const HistoriqueScreen()),
            );
          }
        },
      ),


    );
  }
}