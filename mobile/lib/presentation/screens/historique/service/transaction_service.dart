import 'package:http/http.dart' as http;
import 'dart:convert';
import '../../../../domain/modeles/transaction.dart';
import '../../auth/services/auth_service.dart';

class TransactionService {
  static const String baseUrl = 'http://localhost:6080';

  // Modèle de transaction
  static Future<List<Transaction>> getTransactions(String userId) async {
    try {
      final token = await AuthService.getToken();

      final response = await http.get(
        Uri.parse('$baseUrl/transactions/$userId'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );


      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body);
        return data.map((json) => Transaction.fromJson(json)).toList();
      } else if (response.statusCode == 401) {
        throw Exception('Session expirée, veuillez vous reconnecter');
      } else {
        throw Exception('Erreur lors de la récupération des transactions');
      }
    } catch (e) {
      rethrow;
    }
  }

  //Obtenir le solde total d'un utilisateur avec la route : users/{id_user}/solde
  static Future<double> getSolde(String userId) async {
    try {
      final token = await AuthService.getToken();

      final response = await http.get(
        Uri.parse('$baseUrl/users/$userId/solde'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data['solde'] ?? 0;
      } else if (response.statusCode == 401) {
        throw Exception('Session expirée, veuillez vous reconnecter');
      } else {
        throw Exception('Erreur lors de la récupération du solde');
      }
    } catch (e) {
      rethrow;
    }
  }

  // Grouper les transactions par mois
  static Map<String, List<Transaction>> groupByMonth(
      List<Transaction> transactions) {
    final Map<String, List<Transaction>> grouped = {};

    for (var transaction in transactions) {
      final key = transaction.getMonthYearKey();
      if (!grouped.containsKey(key)) {
        grouped[key] = [];
      }
      grouped[key]!.add(transaction);
    }

    return grouped;
  }
}