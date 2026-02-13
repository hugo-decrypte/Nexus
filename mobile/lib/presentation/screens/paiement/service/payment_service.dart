import 'package:http/http.dart' as http;
import 'dart:convert';
import '../../auth/services/auth_service.dart';

class PaymentService {
  static const String baseUrl = 'http://docketu.iutnc.univ-lorraine.fr:56050';

  /// Créer une transaction de paiement
  /// Le CLIENT (emetteur) paie le COMMERÇANT (recepteur)
  /// POST /transactions
  ///
  /// L'email de confirmation sera automatiquement envoyé par le backend
  static Future<Map<String, dynamic>> createTransaction({
    required String clientId,      // ID du client qui paie
    required String commercantId,  // ID du commerçant qui reçoit
    required int montant,
    String? message,
  }) async {
    try {
      final token = await AuthService.getToken();

      final response = await http.post(
        Uri.parse('$baseUrl/transactions'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode({
          'id_emetteur': clientId,        // Client qui paie
          'id_recepteur': commercantId,   // Commerçant qui reçoit
          'montant': montant,
          'description': message,
        }),
      ).timeout(
        const Duration(seconds: 10),
        onTimeout: () {
          throw Exception('Délai de connexion dépassé');
        },
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        final result = jsonDecode(response.body);
        return result;
      } else if (response.statusCode == 400) {
        final error = jsonDecode(response.body);
        throw Exception(error['message'] ?? 'Solde insuffisant');
      } else if (response.statusCode == 401) {
        throw Exception('Session expirée, veuillez vous reconnecter');
      } else if (response.statusCode == 404) {
        throw Exception('Commerçant introuvable');
      } else {
        final error = jsonDecode(response.body);
        throw Exception(error['message'] ?? 'Erreur lors du paiement');
      }
    } catch (e) {
      rethrow;
    }
  }

  /// Vérifier qu'un commerçant existe
  static Future<bool> commercantExists(String commercantId) async {
    try {
      final token = await AuthService.getToken();

      final response = await http.get(
        Uri.parse('$baseUrl/users/$commercantId'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      ).timeout(
        const Duration(seconds: 5),
      );

      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  /// Obtenir les infos d'un commerçant
  static Future<Map<String, dynamic>?> getCommercantInfo(String commercantId) async {
    try {
      final token = await AuthService.getToken();

      final response = await http.get(
        Uri.parse('$baseUrl/users/$commercantId'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      ).timeout(
        const Duration(seconds: 5),
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
      return null;
    } catch (e) {
      return null;
    }
  }
}