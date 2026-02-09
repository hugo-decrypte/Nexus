import 'package:flutter/material.dart';
import 'package:untitled/presentation/widgets/base/nexus_app_bar.dart';
import '../../../../domain/modeles/transaction.dart';
import '../../../widgets/base/nexus_bottom_nav_bar.dart';
import '../../auth/services/auth_service.dart';
import '../../home/home_screen.dart';
import '../../rechargement/recharge_screen.dart';
import '../service/transaction_service.dart';

class HistoriqueScreen extends StatefulWidget {
  const HistoriqueScreen({super.key});

  @override
  State<HistoriqueScreen> createState() => _HistoriqueScreenState();
}

class _HistoriqueScreenState extends State<HistoriqueScreen> {
  bool _isLoading = true;
  String? _error;
  List<Transaction> _transactions = [];
  Map<String, List<Transaction>> _groupedTransactions = {};
  String? _currentUserId;

  @override
  void initState() {
    super.initState();
    _loadTransactions();
  }

  Future<void> _loadTransactions() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      // Récupérer l'ID utilisateur depuis les données sauvegardées
      final userData = await AuthService.getUserData();
      final userId = userData['userId'];

      if (userId == null) {
        throw Exception('Utilisateur non connecté');
      }

      // ✅ Sauvegarder l'userId
      _currentUserId = userId;

      final transactions = await TransactionService.getTransactions(userId);

      setState(() {
        _transactions = transactions;
        _groupedTransactions = TransactionService.groupByMonth(transactions);
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString().replaceAll('Exception: ', '');
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: NexusAppBar(),
      body: _buildBody(),
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

  Widget _buildBody() {
    if (_isLoading) {
      return const Center(
        child: CircularProgressIndicator(
          color: Color(0xFFFF6B6B),
        ),
      );
    }

    if (_error != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(
                Icons.error_outline,
                size: 64,
                color: Colors.red,
              ),
              const SizedBox(height: 16),
              Text(
                _error!,
                textAlign: TextAlign.center,
                style: const TextStyle(
                  fontSize: 16,
                  color: Colors.black87,
                ),
              ),
              const SizedBox(height: 24),
              ElevatedButton.icon(
                onPressed: _loadTransactions,
                icon: const Icon(Icons.refresh),
                label: const Text('Réessayer'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFFF6B6B),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(
                    horizontal: 24,
                    vertical: 12,
                  ),
                ),
              ),
            ],
          ),
        ),
      );
    }

    if (_transactions.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.receipt_long_outlined,
              size: 80,
              color: Colors.grey[400],
            ),
            const SizedBox(height: 16),
            Text(
              'Aucune transaction',
              style: TextStyle(
                fontSize: 18,
                color: Colors.grey[600],
                fontWeight: FontWeight.w500,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Vos transactions apparaîtront ici',
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey[500],
              ),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _loadTransactions,
      color: const Color(0xFFFF6B6B),
      child: ListView(
        padding: const EdgeInsets.symmetric(vertical: 16),
        children: _buildGroupedTransactions(),
      ),
    );
  }

  List<Widget> _buildGroupedTransactions() {
    final sortedKeys = _groupedTransactions.keys.toList()
      ..sort((a, b) {
        // Tri par date décroissante (plus récent en premier)
        final dateA = _groupedTransactions[a]!.first.date;
        final dateB = _groupedTransactions[b]!.first.date;
        return dateB.compareTo(dateA);
      });

    return sortedKeys.map((monthYear) {
      final transactions = _groupedTransactions[monthYear]!;

      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // En-tête du mois
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 12),
            child: Text(
              monthYear,
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
                color: Color(0xFF3C3C3C),
              ),
            ),
          ),

          // Liste des transactions du mois
          ...transactions.map((transaction) => _buildTransactionCard(transaction)),

          const SizedBox(height: 8),
        ],
      );
    }).toList();
  }

  Widget _buildTransactionCard(Transaction transaction) {
    // ✅ Utiliser l'userId pour déterminer le type
    final isIncome = _currentUserId != null
        ? transaction.isIncomeFor(_currentUserId!)
        : transaction.isIncome;
    final color = isIncome ? Colors.green : const Color(0xFFFF6B6B);
    final icon = isIncome ? Icons.arrow_downward : Icons.arrow_upward;

    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(12),
          onTap: () => _showTransactionDetails(transaction),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Row(
              children: [
                // Icône de type de transaction
                Container(
                  width: 48,
                  height: 48,
                  decoration: BoxDecoration(
                    color: color.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(
                    icon,
                    color: color,
                    size: 24,
                  ),
                ),

                const SizedBox(width: 16),

                // Détails de la transaction
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const SizedBox(height: 4),
                      Text(
                        '${transaction.getFormattedDate()} • ${transaction.getFormattedTime()}',
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.grey[600],
                        ),
                      ),
                    ],
                  ),
                ),

                const SizedBox(width: 12),

                // Montant
                Text(
                  '${isIncome ? '+' : '-'}${transaction.getFormattedAmount()} €',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: color,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  //a modifier pour recevoir les bonnes infos
  void _showTransactionDetails(Transaction transaction) {
    final isIncome = _currentUserId != null
        ? transaction.isIncomeFor(_currentUserId!)
        : transaction.isIncome;

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
            _buildDetailRow('Type', isIncome ? 'Réception' : 'Envoi'),
            _buildDetailRow('Montant', '${transaction.getFormattedAmount()} €'),
            _buildDetailRow('Date', transaction.getFormattedDate()),
            _buildDetailRow('Heure', transaction.getFormattedTime()),
            if (transaction.description != null)
              _buildDetailRow('Description', transaction.description!),
            if (transaction.emetteurId != null)
              _buildDetailRow('Emetteur', transaction.emetteurId!),
            if (transaction.recepteurId != null)
              _buildDetailRow('Destinataire :', transaction.recepteurId!),
            _buildDetailRow('reference transaction', transaction.id),
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