class AuthValidator {
  /// Valide un email
  static String? validateEmail(String? value) {
    if (value == null || value.isEmpty) {
      return 'L\'email est requis';
    }

    // Regex pour validation email
    final emailRegex = RegExp(
      r'^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$',
    );

    if (!emailRegex.hasMatch(value)) {
      return 'Veuillez entrer un email valide';
    }

    return null;
  }

  /// Valide un mot de passe
  static String? validatePassword(String? value, {bool isSignup = false}) {
    if (value == null || value.isEmpty) {
      return 'Le mot de passe est requis';
    }

    if (isSignup) {
      if (value.length < 8) {
        return 'Le mot de passe doit contenir au moins 8 caractères';
      }

      // Vérifier au moins une majuscule
      if (!value.contains(RegExp(r'[A-Z]'))) {
        return 'Le mot de passe doit contenir au moins une majuscule';
      }

      // Vérifier au moins un chiffre
      if (!value.contains(RegExp(r'[0-9]'))) {
        return 'Le mot de passe doit contenir au moins un chiffre';
      }

      // Vérifier au moins un caractère spécial
      if (!value.contains(RegExp(r'[!@#$%^&*(),.?":{}|<>]'))) {
        return 'Le mot de passe doit contenir au moins un caractère spécial';
      }
    } else {
      if (value.length < 6) {
        return 'Le mot de passe doit contenir au moins 6 caractères';
      }
    }

    return null;
  }

  /// Valide la confirmation de mot de passe
  static String? validatePasswordConfirmation(
    String? value,
    String password,
  ) {
    if (value == null || value.isEmpty) {
      return 'Veuillez confirmer votre mot de passe';
    }

    if (value != password) {
      return 'Les mots de passe ne correspondent pas';
    }

    return null;
  }

  /// Valide un nom/prénom
  static String? validateName(String? value, String fieldName) {
    if (value == null || value.isEmpty) {
      return '$fieldName est requis';
    }

    if (value.length < 2) {
      return '$fieldName doit contenir au moins 2 caractères';
    }

    if (!RegExp(r'^[a-zA-ZÀ-ÿ\s-]+$').hasMatch(value)) {
      return '$fieldName ne doit contenir que des lettres';
    }

    return null;
  }

  /// Valide un numéro de téléphone
  static String? validatePhone(String? value) {
    if (value == null || value.isEmpty) {
      return 'Le numéro de téléphone est requis';
    }

    // Accepter différents formats de numéros
    final phoneRegex = RegExp(r'^[+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}$');

    if (!phoneRegex.hasMatch(value.replaceAll(' ', ''))) {
      return 'Veuillez entrer un numéro de téléphone valide';
    }

    return null;
  }

  /// Obtient les exigences du mot de passe pour l'affichage
  static List<PasswordRequirement> getPasswordRequirements(String password) {
    return [
      PasswordRequirement(
        text: 'Au moins 8 caractères',
        isMet: password.length >= 8,
      ),
      PasswordRequirement(
        text: 'Au moins une majuscule',
        isMet: password.contains(RegExp(r'[A-Z]')),
      ),
      PasswordRequirement(
        text: 'Au moins un chiffre',
        isMet: password.contains(RegExp(r'[0-9]')),
      ),
      PasswordRequirement(
        text: 'Au moins un caractère spécial',
        isMet: password.contains(RegExp(r'[!@#$%^&*(),.?":{}|<>]')),
      ),
    ];
  }
}

class PasswordRequirement {
  final String text;
  final bool isMet;

  PasswordRequirement({
    required this.text,
    required this.isMet,
  });
}
