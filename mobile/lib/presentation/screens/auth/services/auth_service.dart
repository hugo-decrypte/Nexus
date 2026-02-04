import 'package:http/http.dart' as http;
import 'dart:convert';

class AuthService {
  static const String baseUrl = 'http://localhost:6080';

  static Future<Map<String, dynamic>> login({
    required String email,
    required String password,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/login'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'email': email,
        'mot_de_passe': password,
      }),
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Email ou mot de passe incorrect');
    }
  }

  static Future<Map<String, dynamic>> register({
    required String nom,
    required String prenom,
    required String email,
    required String password,
  }) async {
    try {
      print('🔄 Tentative de connexion à: $baseUrl/auth/register');

      final response = await http.post(
        Uri.parse('$baseUrl/auth/register'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'nom': nom,
          'prenom': prenom,
          'email': email,
          'mot_de_passe': password,
        }),
      );

      print('✅ Status code: ${response.statusCode}');
      print('📦 Response body: ${response.body}');

      if (response.statusCode == 201 || response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        final error = jsonDecode(response.body);
        throw Exception(error['message'] ?? 'Erreur lors de l\'inscription');
      }
    } on http.ClientException catch (e) {
      print('❌ ClientException: $e');
      throw Exception('Erreur CORS - Configure ton backend');
    } catch (e) {
      print('❌ Erreur: $e');
      rethrow;
    }
  }
}