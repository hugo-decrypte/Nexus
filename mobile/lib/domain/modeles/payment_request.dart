import 'dart:convert';

/// Modèle de facture générée par un commerçant
class PaymentRequest {
  final String commercantId;
  final double montant;
  final String? message;
  final DateTime createdAt;

  PaymentRequest({
    required this.commercantId,
    required this.montant,
    this.message,
    DateTime? createdAt,
  }) : createdAt = createdAt ?? DateTime.now();

  /// Convertir en JSON pour le QR Code
  Map<String, dynamic> toJson() {
    return {
      'commercant_id': commercantId,
      'montant': montant,  // ✅ Sera toujours un double
      'message': message,
      'created_at': createdAt.toIso8601String(),
    };
  }

  /// Créer depuis JSON (scan du QR Code)
  factory PaymentRequest.fromJson(Map<String, dynamic> json) {
    //Gérer à la fois int et double pour le montant
    // Car JSON peut encoder 150.0 comme 150 (int) si pas de décimales
    final montantValue = json['montant'];
    final double montant;

    if (montantValue is int) {
      montant = montantValue.toDouble();
    } else if (montantValue is double) {
      montant = montantValue;
    } else if (montantValue is String) {
      montant = double.parse(montantValue);
    } else {
      montant = double.parse(montantValue.toString());
    }

    return PaymentRequest(
      commercantId: json['commercant_id'] as String,
      montant: montant,
      message: json['message'] as String?,
      createdAt: DateTime.parse(json['created_at'] as String),
    );
  }

  /// Convertir en String pour QR Code
  String toQRString() {
    return jsonEncode(toJson());
  }

  /// Créer depuis String (scan du QR Code)
  factory PaymentRequest.fromQRString(String qrString) {
    final json = jsonDecode(qrString) as Map<String, dynamic>;
    return PaymentRequest.fromJson(json);
  }

  /// Vérifier si la facture est expirée (10 minutes)
  bool isExpired() {
    final now = DateTime.now();
    final difference = now.difference(createdAt);
    return difference.inMinutes > 10;
  }
}