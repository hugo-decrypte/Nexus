import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';

class AuthService {
  static const String baseUrl = 'http://docketu.iutnc.univ-lorraine.fr:56050';
  static const String _tokenKey = 'auth_token';
  static const String _userIdKey = 'user_id';
  static const String _userEmailKey = 'user_email';
  static const String _userRoleKey = 'user_role';

  // ✅ Login et sauvegarde du token
  static Future<Map<String, dynamic>> login({
    required String email,
    required String password,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/auth/login'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'email': email,
          'mot_de_passe': password,
        }),
      );

      print('🔐 Login Status: ${response.statusCode}');
      print('📦 Login Response: ${response.body}');

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        // ✅ Extraire l'ID utilisateur (ajuster selon la structure de ta réponse)
        String? userId;

        // Cas 1 : Si l'API retourne { "token": "...", "user": { "id": "..." } }
        if (data['user'] != null && data['user']['id'] != null) {
          userId = data['user']['id'].toString();
        }
        // Cas 2 : Si l'API retourne { "token": "...", "userId": "..." }
        else if (data['userId'] != null) {
          userId = data['userId'].toString();
        }
        // Cas 3 : Si l'API retourne { "token": "...", "user_id": "..." }
        else if (data['user_id'] != null) {
          userId = data['user_id'].toString();
        }
        // Cas 4 : Si l'API retourne { "token": "...", "id": "..." }
        else if (data['id'] != null) {
          userId = data['id'].toString();
        }

        print('✅ User ID extrait: $userId');

        // Sauvegarder automatiquement le token et les infos user
        await saveUserData(
          token: data['token'],
          userId: userId,
          email: data['user']?['email']?.toString() ?? data['email']?.toString() ?? email,
          role: data['user']?['role']?.toString() ?? data['role']?.toString(),
        );

        return data;
      } else {
        throw Exception('Email ou mot de passe incorrect');
      }
    } catch (e) {
      print('❌ Erreur login: $e');
      rethrow;
    }
  }

  static Future<void> register({
    required String nom,
    required String prenom,
    required String email,
    required String password,
    required String role,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/register'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'nom': nom,
        'prenom': prenom,
        'email': email,
        'mot_de_passe': password,
        'role': role,
      }),
    );

    print('📝 Register status: ${response.statusCode}');
    print('📦 Register response: ${response.body}');

    Map<String, dynamic>? data;

    try {
      data = jsonDecode(response.body);
    } catch (_) {
      data = null;
    }

    // ✅ Succès
    if (response.statusCode == 200 || response.statusCode == 201) {
      return;
    }

    if (response.statusCode == 400) {
      throw Exception(
        data?['message'] ?? 'Données invalides.',
      );
    }

    if (response.statusCode >= 500) {
      throw Exception(
        'Cette adresse mail existe déjà.',
      );
    }

    throw Exception(
      data?['message'] ?? 'Erreur lors de l\'inscription.',
    );
  }



  //Sauvegarder toutes les données utilisateur
  static Future<void> saveUserData({
    required String token,
    String? userId,
    String? email,
    String? role,
  }) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_tokenKey, token);
    if (userId != null) {
      await prefs.setString(_userIdKey, userId);
    }
    if (email != null) {
      await prefs.setString(_userEmailKey, email);
    }
  }

  //Récupérer le token
  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_tokenKey);
  }

  //Récupérer les infos user
  static Future<Map<String, String?>> getUserData() async {
    final prefs = await SharedPreferences.getInstance();
    final data = {
      'token': prefs.getString(_tokenKey),
      'userId': prefs.getString(_userIdKey),
      'email': prefs.getString(_userEmailKey),
      'role': prefs.getString(_userRoleKey),
    };
    return data;
  }

  //Vérifier si l'utilisateur est connecté
  static Future<bool> isLoggedIn() async {
    final token = await getToken();
    return token != null && token.isNotEmpty;
  }

  // Déconnexion
  static Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
    await prefs.remove(_userIdKey);
    await prefs.remove(_userEmailKey);
  }

  //Vérifier si le token est valide (route à créer)
  static Future<bool> validateToken() async {
    final token = await getToken();
    if (token == null) return false;

    try {
      final response = await http.get(
        Uri.parse('$baseUrl/auth/validate'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }
}