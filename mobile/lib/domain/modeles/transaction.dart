class Transaction {
  final String id;
  final double montant;
  final String? hash;
  final String? emetteurId;
  final String? recepteurId;
  final String? compteId;
  final String? description;
  final DateTime date;

  Transaction({
    required this.id,
    required this.montant,
    this.hash,
    this.emetteurId,
    this.recepteurId,
    this.compteId,
    this.description,
    required this.date,
  });

  factory Transaction.fromJson(Map<String, dynamic> json) {
    return Transaction(
      id: json['id']?.toString() ?? '',
      montant: double.tryParse(json['montant']?.toString() ?? '0') ?? 0.0,
      hash: json['hash']?.toString(),
      emetteurId: json['emetteur_id']?.toString(),
      recepteurId: json['recepteur_id']?.toString(),
      compteId: json['compte_id']?.toString(),
      description: json['description']?.toString() ?? json['libelle']?.toString(),
      date: json['date'] != null
          ? DateTime.parse(json['date'])
          : (json['created_at'] != null
          ? DateTime.parse(json['created_at'])
          : DateTime.now()),
    );
  }

  // Déterminer si c'est un gain
  bool isIncomeFor(String userId) {
    return recepteurId == userId;
  }

  // Déterminer si c'est une perte
  bool isExpenseFor(String userId) {
    return emetteurId == userId;
  }

  // Type de transaction
  String getTypeFor(String userId) {
    if (isIncomeFor(userId)) return 'CREDIT';
    if (isExpenseFor(userId)) return 'DEBIT';
    return 'INCONNU';
  }

  // Pour la compatibilité avec les méthodes d'affichage
  bool get isIncome => false;
  bool get isExpense => false;

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

  // Obtenir un label par défaut pour l'affichage
  String getDisplayLabel(String currentUserId) {
    if (description != null && description!.isNotEmpty) {
      return description!;
    }

    if (isIncomeFor(currentUserId)) {
      return 'Réception';
    } else if (isExpenseFor(currentUserId)) {
      return 'Envoi';
    }
    return 'INCONNU';}
}