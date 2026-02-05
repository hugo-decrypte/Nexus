import 'package:flutter/material.dart';
import 'package:untitled/presentation/widgets/base/nexus_app_bar.dart';

import '../../widgets/base/nexus_bottom_nav_bar.dart';
import '../home/home_screen.dart';
import '../rechargement/recharge_screen.dart';

class HistoriqueScreen extends StatelessWidget {
  const HistoriqueScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        backgroundColor: const Color(0xFFF5F5F5),
    // bar du haut
    appBar: const NexusAppBar(),
    body: const Center(
      child: Text(
        'Historique des transactions',
        style: TextStyle(fontSize: 18, color: Colors.black54),
        ),
      ),
      bottomNavigationBar: NexusBottomNavBar(
        currentIndex: 2,
        onTap: (index) {
          if (index == 2) return;

          if (index == 0) {
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(builder: (_) => const HomeScreen()),
            );
          }

          if (index == 1) {
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(builder: (_) => const RechargeScreen()),
            );
          }
        },
      ),

    );
    }
}