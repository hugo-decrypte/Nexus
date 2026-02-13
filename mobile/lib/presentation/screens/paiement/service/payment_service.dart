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
    required double montant,
    String? message,
  }) async {
    try {
      final token = await AuthService.getToken();

      print('═══════════════════════════════════════');
      print('💳 CRÉATION TRANSACTION');
      print('═══════════════════════════════════════');
      print('👤 Client ID: $clientId');
      print('🏪 Commerçant ID: $commercantId');
      print('💰 Montant: $montant PO');
      print('📝 Message: $message');
      print('🔑 Token: ${token?.substring(0, 20)}...');

      // ✅ CORRECTION: Convertir le montant en double (float)
      final body = {
        'id_emetteur': clientId,
        'id_recepteur': commercantId,
        'montant': montant.toDouble(),  // ✅ Conversion en float
        'description': message,
      };

      print('───────────────────────────────────────');
      print('📤 REQUEST');
      print('URL: $baseUrl/transactions');
      print('Body: ${jsonEncode(body)}');
      print('───────────────────────────────────────');

      final response = await http.post(
        Uri.parse('$baseUrl/transactions'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode(body),
      ).timeout(
        const Duration(seconds: 10),
        onTimeout: () {
          throw Exception('Délai de connexion dépassé');
        },
      );

      print('───────────────────────────────────────');
      print('📥 RESPONSE');
      print('Status Code: ${response.statusCode}');
      print('Body: ${response.body}');
      print('═══════════════════════════════════════');

      if (response.statusCode == 200 || response.statusCode == 201) {
        final result = jsonDecode(response.body);
        print('✅ Transaction créée avec succès');
        return result;
      } else {
        print('❌ ERREUR ${response.statusCode}');

        try {
          final error = jsonDecode(response.body);

          // Extraire le message d'erreur le plus pertinent
          String errorMessage = 'Erreur lors du paiement';

          if (error is Map) {
            if (error.containsKey('message')) {
              errorMessage = error['message'].toString();
            } else if (error.containsKey('error')) {
              errorMessage = error['error'].toString();
            } else if (error.containsKey('exception') && error['exception'] is List) {
              // Extraire le message de la première exception
              final firstException = error['exception'][0];
              if (firstException is Map && firstException.containsKey('message')) {
                errorMessage = firstException['message'].toString();
              }
            } else {
              errorMessage = error.toString();
            }
          }

          throw Exception(errorMessage);
        } catch (jsonError) {
          throw Exception('Erreur ${response.statusCode}: ${response.body}');
        }
      }
    } catch (e) {
      print('❌ EXCEPTION: $e');
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
      print('❌ Erreur vérification commerçant: $e');
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
      print('❌ Erreur infos commerçant: $e');
      return null;
    }
  }
}