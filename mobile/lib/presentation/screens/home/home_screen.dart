import 'package:flutter/material.dart';
import 'package:untitled/presentation/widgets/base/nexus_app_bar.dart';
import 'package:untitled/presentation/widgets/base/nexus_bottom_nav_bar.dart';

// écrans de paiement
import '../historique/screen/historique_screen.dart';
import '../paiement/receive_screen.dart';
import '../paiement/send_screen.dart';
import '../rechargement/recharge_screen.dart';


/// Écran principal de l’application (Accueil)
class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        backgroundColor: const Color(0xFFF5F5F5),
        // bar du haut
        appBar: const NexusAppBar(),

        /// =====================
        /// CONTENU PRINCIPAL
        /// =====================
        body: Column(
          children: [

            /// =====================
            /// CARTE DU SOLDE
            /// =====================
            Container(
              height: 250,
              margin: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: const Color.fromARGB(255, 53, 43, 43),
                    blurRadius: 4,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(16),
                child: Stack(
                  children: [
                    // Fond dégradé
                    Container(
                      decoration: const BoxDecoration(
                        gradient: LinearGradient(
                          colors: [
                            Color(0xFFFF6B6B),
                            Color.fromARGB(255, 126, 45, 146),
                          ],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                      ),
                    ),

                    Opacity(
                      opacity: 0.10,
                      child: Image.asset(
                        'assets/images/low-poly-background.png',
                        width: double.infinity,
                        height: double.infinity,
                        fit: BoxFit.cover,
                      ),
                    ),

                    //Contenu de la carte
                    Padding(
                      padding: const EdgeInsets.all(24),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'NEXUS',
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 24,
                              fontWeight: FontWeight.bold,
                            ),
                          ),

                          const SizedBox(height: 24),

                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Row(
                                children: [
                                  const Text(
                                    '15 250',
                                    style: TextStyle(
                                      color: Colors.white,
                                      fontSize: 36,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  const SizedBox(width: 8),
                                  Container(
                                    padding: const EdgeInsets.all(6),
                                    decoration: const BoxDecoration(
                                      color: Color(0xFFFFC107),
                                      shape: BoxShape.circle,
                                    ),
                                    child: const Icon(
                                      Icons.monetization_on,
                                      color: Colors.white,
                                      size: 20,
                                    ),
                                  ),
                                ],
                              ),
                              Container(
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  color: Colors.white.withOpacity(0.2),
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: const Icon(
                                  Icons.account_balance_wallet_rounded,
                                  color: Color(0xFFFFC107),
                                  size: 24,
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),

            /// =====================
            /// BOUTONS D’ACTION
            /// =====================
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Row(
                children: [
                  // Bouton "Envoyer"
                  Expanded(
                    child: _ActionButton(
                      icon: Icons.qr_code_scanner,
                      label: 'Envoyer des PO',
                      onTap: () {
                        // Navigation vers l’écran d’envoi
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const SendScreen(),
                          ),
                        );
                      },
                    ),
                  ),

                  const SizedBox(width: 16),

                  // Bouton "Recevoir"
                  Expanded(
                    child: _ActionButton(
                      icon: Icons.camera_alt,
                      label: 'Recevoir des PO',
                      onTap: () {
                        // Navigation vers l’écran de réception
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const ReceiveScreen(),
                          ),
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),

            /// =====================
            /// TITRE TRANSACTIONS
            /// =====================
            Padding(
              padding: const EdgeInsets.all(16),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Transactions récentes',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
                      color: Color(0xFF3C3C3C),
                    ),
                  ),

                  // Bouton "Voir tout"
                  // Faire l'action associé (genre faire en sorte qu'on voit les 20 dernieres transa max + qui date de moins de 15 jours, triés par mois
                  TextButton(
                    onPressed: () {},
                    child: const Text(
                      'Voir tout',
                      style: TextStyle(
                        color: Color(0xFFFF6B6B),
                        fontSize: 14,
                      ),
                    ),
                  ),
                ],
              ),
            ),

            /// =====================
            /// LISTE DES TRANSACTIONS
            /// (integrer les transactions via l'api)
            /// =====================
            Expanded(
              child: ListView.builder(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                itemCount: 20, // Transactions factices
                itemBuilder: (context, index) {
                  return Container(
                    margin: const EdgeInsets.only(bottom: 12),
                    padding: const EdgeInsets.all(16),
                    height: 60,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(
                        color: const Color(0xFFE0E0E0),
                        width: 1,
                      ),
                    ),
                  );
                },
              ),
            ),
          ],
        ),
      bottomNavigationBar: NexusBottomNavBar(
        currentIndex: 0,
        onTap: (index) {
          if (index == 0) return;

          if (index == 1) {
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(builder: (_) => const RechargeScreen()),
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

/// =====================
/// WIDGET BOUTON RÉUTILISABLE
/// =====================
class _ActionButton extends StatelessWidget {
  final IconData icon;
  final String label;
  final VoidCallback onTap;

  const _ActionButton({
    required this.icon,
    required this.label,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      // Action au clic
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),

      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: const Color(0xFFFCD8DA),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: const Color(0xFFFFA6B0),
            width: 1,
          ),
        ),

        child: Column(
          children: [
            // Icône du bouton
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(
                  color: const Color(0xFFE0E0E0),
                  width: 1,
                ),
              ),
              child: Icon(
                icon,
                size: 32,
                color: const Color(0xFF3C3C3C),
              ),
            ),

            const SizedBox(height: 12),

            // Texte du bouton
            Text(
              label,
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w600,
                color: Color(0xFF3C3C3C),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
