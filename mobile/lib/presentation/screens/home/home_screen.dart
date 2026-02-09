import 'package:flutter/material.dart';
import 'package:untitled/presentation/widgets/base/nexus_app_bar.dart';
import 'package:untitled/presentation/widgets/base/nexus_bottom_nav_bar.dart';
import '../../../../domain/modeles/transaction.dart';

// Écrans de paiement
import '../auth/services/auth_service.dart';
import '../historique/screen/historique_screen.dart';
import '../historique/service/transaction_service.dart';
import '../paiement/receive_screen.dart';
import '../paiement/send_screen.dart';
import '../rechargement/recharge_screen.dart';

/// Écran principal de l'application (Accueil)
class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  double _solde = 0;
  List<Transaction> _recentTransactions = [];
  bool _isLoadingSolde = true;
  bool _isLoadingTransactions = true;
  String? _currentUserId;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  /// Charger le solde et les transactions
  Future<void> _loadData() async {
    try {
      final userData = await AuthService.getUserData();
      final userId = userData['userId'];

      if (userId == null) {
        throw Exception('Utilisateur non connecté');
      }

      setState(() {
        _currentUserId = userId;
      });

      // Charger le solde et les transactions en parallèle
      await Future.wait([
        _loadSolde(userId),
        _loadTransactions(userId),
      ]);
    } catch (e) {
      print('❌ Erreur lors du chargement: $e');
    }
  }

  /// Charger le solde
  Future<void> _loadSolde(String userId) async {
    try {
      final solde = await TransactionService.getSolde(userId);
      setState(() {
        _solde = solde;
        _isLoadingSolde = false;
      });
    } catch (e) {
      setState(() {
        _isLoadingSolde = false;
      });
      print('❌ Erreur solde: $e');
    }
  }

  /// Charger les 5 transactions les plus récentes
  Future<void> _loadTransactions(String userId) async {
    try {
      final transactions = await TransactionService.getTransactions(userId);

      // Trier par date décroissante et prendre les 5 plus récentes
      transactions.sort((a, b) => b.date.compareTo(a.date));
      final recentTransactions = transactions.take(5).toList();

      setState(() {
        _recentTransactions = recentTransactions;
        _isLoadingTransactions = false;
      });
    } catch (e) {
      setState(() {
        _isLoadingTransactions = false;
      });
      print('❌ Erreur transactions: $e');
    }
  }

  /// Rafraîchir les données
  Future<void> _refreshData() async {
    setState(() {
      _isLoadingSolde = true;
      _isLoadingTransactions = true;
    });
    await _loadData();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      appBar: const NexusAppBar(),
      body: RefreshIndicator(
        onRefresh: _refreshData,
        color: const Color(0xFFFF6B6B),
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
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

                      // Contenu de la carte
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
                                    // ✅ Affichage dynamique du solde
                                    _isLoadingSolde
                                        ? const SizedBox(
                                      width: 100,
                                      height: 36,
                                      child: Center(
                                        child: CircularProgressIndicator(
                                          color: Colors.white,
                                          strokeWidth: 2,
                                        ),
                                      ),
                                    )
                                        : Text(
                                      _solde.toString(),
                                      style: const TextStyle(
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
              /// BOUTONS D'ACTION
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
                    TextButton(
                      onPressed: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => const HistoriqueScreen(),
                          ),
                        );
                      },
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
              /// =====================
              _isLoadingTransactions
                  ? const Padding(
                padding: EdgeInsets.all(40.0),
                child: Center(
                  child: CircularProgressIndicator(
                    color: Color(0xFFFF6B6B),
                  ),
                ),
              )
                  : _recentTransactions.isEmpty
                  ? Padding(
                padding: const EdgeInsets.all(40.0),
                child: Column(
                  children: [
                    Icon(
                      Icons.receipt_long_outlined,
                      size: 64,
                      color: Colors.grey[400],
                    ),
                    const SizedBox(height: 12),
                    Text(
                      'Aucune transaction récente',
                      style: TextStyle(
                        fontSize: 14,
                        color: Colors.grey[600],
                      ),
                    ),
                  ],
                ),
              )
                  : ListView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                padding: const EdgeInsets.symmetric(horizontal: 16),
                itemCount: _recentTransactions.length,
                itemBuilder: (context, index) {
                  final transaction = _recentTransactions[index];
                  return _TransactionItem(
                    transaction: transaction,
                    currentUserId: _currentUserId,
                  );
                },
              ),

              const SizedBox(height: 80),
            ],
          ),
        ),
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

/// =====================
/// WIDGET ITEM DE TRANSACTION
/// =====================
class _TransactionItem extends StatelessWidget {
  final Transaction transaction;
  final String? currentUserId;

  const _TransactionItem({
    required this.transaction,
    this.currentUserId,
  });

  @override
  Widget build(BuildContext context) {
    // ✅ Déterminer si c'est un gain ou une perte
    final isIncome = currentUserId != null && transaction.isIncomeFor(currentUserId!);
    final color = isIncome ? Colors.green : const Color(0xFFFF6B6B);
    final icon = isIncome ? Icons.arrow_downward : Icons.arrow_upward;

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: const Color(0xFFE0E0E0),
          width: 1,
        ),
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(12),
          onTap: () => _showTransactionDetails(context),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Row(
              children: [
                // Icône de type
                Container(
                  width: 40,
                  height: 40,
                  decoration: BoxDecoration(
                    color: color.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Icon(
                    icon,
                    color: color,
                    size: 20,
                  ),
                ),

                const SizedBox(width: 16),

                // Détails
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        currentUserId != null
                            ? transaction.getDisplayLabel(currentUserId!)
                            : (transaction.description ?? 'Transaction'),
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                          color: Color(0xFF3C3C3C),
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        transaction.getFormattedDate(),
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.grey[600],
                        ),
                      ),
                    ],
                  ),
                ),

                // Montant
                Text(
                  '${isIncome ? '+' : '-'}${transaction.getFormattedAmount()}',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                    color: color,
                  ),
                ),

                const SizedBox(width: 4),

                // Icône PO
                Container(
                  padding: const EdgeInsets.all(4),
                  decoration: const BoxDecoration(
                    color: Color(0xFFFFC107),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.monetization_on,
                    color: Colors.white,
                    size: 12,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  void _showTransactionDetails(BuildContext context) {
    final isIncome = currentUserId != null && transaction.isIncomeFor(currentUserId!);
    final typeLabel = isIncome ? 'Réception' : 'Envoi';

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
        ),
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(
              child: Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: Colors.grey[300],
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
            ),
            const SizedBox(height: 24),
            const Text(
              'Détails de la transaction',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 24),
            _buildDetailRow('Type', typeLabel),
            _buildDetailRow('Montant', '${transaction.getFormattedAmount()} PO'),
            _buildDetailRow('Date', transaction.getFormattedDate()),
            _buildDetailRow('Heure', transaction.getFormattedTime()),
            if (transaction.description != null)
              _buildDetailRow('Description', transaction.description!),
            if (transaction.emetteurId != null)
              _buildDetailRow('Émetteur', transaction.emetteurId!),
            if (transaction.recepteurId != null)
              _buildDetailRow('Récepteur', transaction.recepteurId!),
            _buildDetailRow('ID', transaction.id),
            const SizedBox(height: 24),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () => Navigator.pop(context),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFFF6B6B),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
                child: const Text('Fermer'),
              ),
            ),
            SizedBox(height: MediaQuery.of(context).padding.bottom),
          ],
        ),
      ),
    );
  }

  Widget _buildDetailRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 100,
            child: Text(
              label,
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey[600],
              ),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: const TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w500,
                color: Color(0xFF3C3C3C),
              ),
            ),
          ),
        ],
      ),
    );
  }
}