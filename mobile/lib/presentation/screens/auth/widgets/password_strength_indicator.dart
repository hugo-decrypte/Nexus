import 'package:flutter/material.dart';
import '../utils/auth_validator.dart';

class PasswordStrengthIndicator extends StatelessWidget {
  final String password;

  const PasswordStrengthIndicator({
    super.key,
    required this.password,
  });

  @override
  Widget build(BuildContext context) {
    if (password.isEmpty) return const SizedBox.shrink();

    final requirements = AuthValidator.getPasswordRequirements(password);
    final metCount = requirements.where((r) => r.isMet).length;
    final strength = metCount / requirements.length;

    Color strengthColor;
    String strengthText;

    if (strength < 0.5) {
      strengthColor = const Color(0xFFE53935);
      strengthText = 'Faible';
    } else if (strength < 0.75) {
      strengthColor = const Color(0xFFFB8C00);
      strengthText = 'Moyen';
    } else if (strength < 1.0) {
      strengthColor = const Color(0xFFFDD835);
      strengthText = 'Bon';
    } else {
      strengthColor = const Color(0xFF43A047);
      strengthText = 'Fort';
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const SizedBox(height: 8),
        Row(
          children: [
            Expanded(
              child: ClipRRect(
                borderRadius: BorderRadius.circular(4),
                child: LinearProgressIndicator(
                  value: strength,
                  backgroundColor: Colors.grey[200],
                  valueColor: AlwaysStoppedAnimation<Color>(strengthColor),
                  minHeight: 6,
                ),
              ),
            ),
            const SizedBox(width: 12),
            Text(
              strengthText,
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w600,
                color: strengthColor,
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        ...requirements.map((req) => Padding(
              padding: const EdgeInsets.only(bottom: 6),
              child: Row(
                children: [
                  Icon(
                    req.isMet ? Icons.check_circle : Icons.cancel,
                    size: 16,
                    color: req.isMet
                        ? const Color(0xFF43A047)
                        : Colors.grey[400],
                  ),
                  const SizedBox(width: 8),
                  Text(
                    req.text,
                    style: TextStyle(
                      fontSize: 12,
                      color: req.isMet
                          ? const Color(0xFF43A047)
                          : Colors.grey[600],
                      fontWeight:
                          req.isMet ? FontWeight.w600 : FontWeight.normal,
                    ),
                  ),
                ],
              ),
            )),
      ],
    );
  }
}
