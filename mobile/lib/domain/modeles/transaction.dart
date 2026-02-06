// Modèle Transaction
class Transaction {
  final String id;
  final String compteId;
  final double montant;
  final String type; // 'DEBIT' ou 'CREDIT'
  final String? description;
  final String? categorie;
  final DateTime date;

  Transaction({
    required this.id,
    required this.compteId,
    required this.montant,
    required this.type,
    this.description,
    this.categorie,
    required this.date,
  });

  factory Transaction.fromJson(Map<String, dynamic> json) {
    return Transaction(
      id: json['id']?.toString() ?? '',
      compteId: json['compte_id']?.toString() ?? '',
      montant: double.tryParse(json['montant']?.toString() ?? '0') ?? 0.0,
      type: json['type']?.toString().toUpperCase() ?? 'DEBIT',
      description: json['description']?.toString(),
      categorie: json['categorie']?.toString(),
      date: DateTime.parse(json['date'] ?? DateTime.now().toIso8601String()),
    );
  }

  bool get isIncome => type.toUpperCase() == 'CREDIT';
  bool get isExpense => type.toUpperCase() == 'DEBIT';

  String getMonthYearKey() {
    final months = [
      'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
      'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
    ];
    return '${months[date.month - 1]} ${date.year}';
  }

  String getFormattedDate() {
    return '${date.day.toString().padLeft(2, '0')}/${date.month.toString().padLeft(2, '0')}/${date.year}';
  }

  String getFormattedTime() {
    return '${date.hour.toString().padLeft(2, '0')}:${date.minute.toString().padLeft(2, '0')}';
  }

  String getFormattedAmount() {
    return montant.toStringAsFixed(2);
  }
}
