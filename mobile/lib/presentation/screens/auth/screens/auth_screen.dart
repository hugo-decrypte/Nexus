import 'package:flutter/material.dart';
import '../../home/home_screen.dart';
import '../services/auth_service.dart';
import '../widgets/custom_text_field.dart';
import '../widgets/custom_button.dart';
import '../widgets/error_message.dart';
import '../utils/form_validators.dart';

class AuthScreen extends StatefulWidget {
  const AuthScreen({super.key});

  @override
  State<AuthScreen> createState() => _AuthScreenState();
}

class _AuthScreenState extends State<AuthScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;

  final _loginFormKey = GlobalKey<FormState>();
  final _registerFormKey = GlobalKey<FormState>();

  // Login controllers
  final _loginEmailController = TextEditingController();
  final _loginPasswordController = TextEditingController();

  // Register controllers
  final _registerNameController = TextEditingController();
  final _registerPrenomController = TextEditingController();
  final _registerEmailController = TextEditingController();
  final _registerPasswordController = TextEditingController();
  final _registerValideMDPController = TextEditingController();

  // ✅ Rôle sélectionné ('client' par défaut)
  String _selectedRole = 'client';

  // State
  bool _isLoginLoading = false;
  bool _isRegisterLoading = false;
  bool _showLoginPassword = false;
  bool _showRegisterPassword = false;
  bool _showConfirmPassword = false;
  String? _loginError;
  String? _registerError;
  String? _successMessage;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _tabController.addListener(() {
      setState(() {
        _loginError = null;
        _registerError = null;
        _successMessage = null;
      });
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    _loginEmailController.dispose();
    _loginPasswordController.dispose();
    _registerNameController.dispose();
    _registerPrenomController.dispose();
    _registerEmailController.dispose();
    _registerPasswordController.dispose();
    _registerValideMDPController.dispose();
    super.dispose();
  }

  Future<void> _handleLogin() async {
    setState(() {
      _loginError = null;
      _successMessage = null;
    });

    if (!_loginFormKey.currentState!.validate()) return;

    setState(() => _isLoginLoading = true);

    try {
      await AuthService.login(
        email: _loginEmailController.text.trim(),
        password: _loginPasswordController.text,
      );

      if (!mounted) return;

      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const HomeScreen()),
      );
    } catch (e) {
      setState(() => _loginError = e.toString().replaceAll('Exception: ', ''));
    } finally {
      if (mounted) setState(() => _isLoginLoading = false);
    }
  }

  Future<void> _handleRegister() async {
    setState(() {
      _registerError = null;
      _successMessage = null;
    });

    if (!_registerFormKey.currentState!.validate()) return;

    setState(() => _isRegisterLoading = true);

    try {
      await AuthService.register(
        nom: _registerNameController.text.trim(),
        prenom: _registerPrenomController.text.trim(),
        email: _registerEmailController.text.trim(),
        password: _registerPasswordController.text,
        role: _selectedRole,
      );

      if (!mounted) return;

      // ✅ Succès uniquement si aucune exception
      _showEmailVerificationDialog();

    } catch (e) {
      if (!mounted) return;

      String errorMessage =
      e.toString().replaceAll('Exception: ', '');

      if (errorMessage.contains('duplicate key') ||
          errorMessage.contains('already exists')) {
        errorMessage = 'Cette adresse email est déjà utilisée.';
      }

      setState(() => _registerError = errorMessage);

    } finally {
      if (mounted) {
        setState(() => _isRegisterLoading = false);
      }
    }
  }


  /// ✅ Dialog de vérification email
  void _showEmailVerificationDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
        contentPadding: const EdgeInsets.all(32),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Icône email avec animation
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: const Color(0xFFFF6B6B).withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: const Icon(
                Icons.mark_email_unread_rounded,
                size: 64,
                color: Color(0xFFFF6B6B),
              ),
            ),

            const SizedBox(height: 24),

            // Titre
            const Text(
              'Vérifiez votre email',
              style: TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.bold,
                color: Color(0xFF3C3C3C),
              ),
              textAlign: TextAlign.center,
            ),

            const SizedBox(height: 16),

            // Message principal
            Text(
              'Un email de vérification a été envoyé à :',
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey[600],
              ),
              textAlign: TextAlign.center,
            ),

            const SizedBox(height: 8),

            // Email de l'utilisateur
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              decoration: BoxDecoration(
                color: const Color(0xFFF5F5F5),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                _registerEmailController.text.trim(),
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: Color(0xFFFF6B6B),
                ),
                textAlign: TextAlign.center,
              ),
            ),

            const SizedBox(height: 16),

            // Instructions
            Text(
              'Veuillez cliquer sur le lien dans l\'email pour activer votre compte.',
              style: TextStyle(
                fontSize: 13,
                color: Colors.grey[700],
                height: 1.4,
              ),
              textAlign: TextAlign.center,
            ),

            const SizedBox(height: 24),

            // Note importante
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.blue.shade50,
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: Colors.blue.shade200),
              ),
              child: Row(
                children: [
                  Icon(Icons.info_outline, size: 18, color: Colors.blue.shade700),
                  const SizedBox(width: 10),
                ],
              ),
            ),

            const SizedBox(height: 24),

            // Bouton OK
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () {
                  Navigator.pop(context); // Fermer le dialog
                  _tabController.animateTo(0); // Retour à l'onglet login
                  _loginEmailController.text = _registerEmailController.text;
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFFF6B6B),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                  elevation: 0,
                ),
                child: const Text(
                  'J\'ai compris',
                  style: TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: Column(
          children: [
            _buildHeader(),
            _buildTabBar(),
            Expanded(
              child: TabBarView(
                controller: _tabController,
                children: [
                  _buildLoginTab(),
                  _buildRegisterTab(),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 40, horizontal: 24),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [Color(0xFFFF6B6B), Color(0xFFFF8080)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(20),
            ),
            child: const Icon(Icons.account_balance_wallet, size: 48, color: Colors.white),
          ),
          const SizedBox(height: 20),
          const Text(
            'NEXUS',
            style: TextStyle(fontSize: 32, fontWeight: FontWeight.bold, color: Color(0xFFFF6B6B)),
          ),
        ],
      ),
    );
  }

  Widget _buildTabBar() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 24),
      decoration: BoxDecoration(
        color: const Color(0xFFF5F5F5),
        borderRadius: BorderRadius.circular(12),
      ),
      child: TabBar(
        controller: _tabController,
        indicator: BoxDecoration(color: const Color(0xFFFF6B6B), borderRadius: BorderRadius.circular(12)),
        labelColor: Colors.white,
        unselectedLabelColor: const Color(0xFF3C3C3C),
        labelStyle: const TextStyle(fontSize: 15, fontWeight: FontWeight.w600),
        tabs: const [Tab(text: 'Connexion'), Tab(text: 'Inscription')],
      ),
    );
  }

  Widget _buildLoginTab() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Form(
        key: _loginFormKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 20),

            if (_loginError != null) ...[
              ErrorMessage(message: _loginError!, onDismiss: () => setState(() => _loginError = null)),
              const SizedBox(height: 20),
            ],

            if (_successMessage != null && _tabController.index == 0) ...[
              SuccessMessage(message: _successMessage!, onDismiss: () => setState(() => _successMessage = null)),
              const SizedBox(height: 20),
            ],

            CustomTextField(
              controller: _loginEmailController,
              label: 'Email',
              hintText: 'votre.email@exemple.com',
              prefixIcon: Icons.email_outlined,
              keyboardType: TextInputType.emailAddress,
              validator: FormValidators.validateEmail,
            ),
            const SizedBox(height: 20),

            CustomTextField(
              controller: _loginPasswordController,
              label: 'Mot de passe',
              hintText: '••••••••',
              prefixIcon: Icons.lock_outline,
              obscureText: !_showLoginPassword,
              validator: FormValidators.validatePassword,
              suffixIcon: IconButton(
                icon: Icon(_showLoginPassword ? Icons.visibility_off : Icons.visibility, color: Colors.black38, size: 20),
                onPressed: () => setState(() => _showLoginPassword = !_showLoginPassword),
              ),
            ),
            const SizedBox(height: 12),

            CustomButton(text: 'Se connecter', onPressed: _handleLogin, isLoading: _isLoginLoading),

            const SizedBox(height: 20),
            Row(
              children: [
                Expanded(child: Divider(color: Colors.grey.shade300)),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Text('ou', style: TextStyle(color: Colors.grey.shade600, fontSize: 14)),
                ),
                Expanded(child: Divider(color: Colors.grey.shade300)),
              ],
            ),
            const SizedBox(height: 20),

            CustomButton(
              text: 'Continuer avec Google',
              onPressed: () {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Fonctionnalité à venir'), duration: Duration(seconds: 2)),
                );
              },
              isOutlined: true,
              icon: Icons.g_mobiledata,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildRegisterTab() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Form(
        key: _registerFormKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 20),

            if (_registerError != null) ...[
              ErrorMessage(message: _registerError!, onDismiss: () => setState(() => _registerError = null)),
              const SizedBox(height: 20),
            ],

            CustomTextField(
              controller: _registerPrenomController,
              label: 'Prénom',
              hintText: 'Prénom',
              prefixIcon: Icons.person_2_outlined,
              validator: FormValidators.validateName,
            ),
            const SizedBox(height: 20),

            CustomTextField(
              controller: _registerNameController,
              label: 'Nom',
              hintText: 'Nom',
              prefixIcon: Icons.person_outline,
              validator: FormValidators.validateName,
            ),
            const SizedBox(height: 20),

            CustomTextField(
              controller: _registerEmailController,
              label: 'Email',
              hintText: 'email@mail.com',
              prefixIcon: Icons.email,
              keyboardType: TextInputType.emailAddress,
              validator: FormValidators.validateEmail,
            ),
            const SizedBox(height: 20),

            CustomTextField(
              controller: _registerPasswordController,
              label: 'Mot de passe',
              hintText: '••••••••',
              prefixIcon: Icons.lock_outline,
              obscureText: !_showRegisterPassword,
              validator: FormValidators.validatePassword,
              suffixIcon: IconButton(
                icon: Icon(_showRegisterPassword ? Icons.visibility_off : Icons.visibility, color: Colors.black38, size: 20),
                onPressed: () => setState(() => _showRegisterPassword = !_showRegisterPassword),
              ),
            ),
            const SizedBox(height: 20),

            CustomTextField(
              controller: _registerValideMDPController,
              label: 'Confirmer le mot de passe',
              hintText: '••••••••',
              prefixIcon: Icons.lock_outline,
              obscureText: !_showConfirmPassword,
              validator: (value) => FormValidators.validateConfirmPassword(value, _registerPasswordController.text),
              suffixIcon: IconButton(
                icon: Icon(_showConfirmPassword ? Icons.visibility_off : Icons.visibility, color: Colors.black38, size: 20),
                onPressed: () => setState(() => _showConfirmPassword = !_showConfirmPassword),
              ),
            ),
            const SizedBox(height: 24),

            // Sélecteur de rôle
            _buildRoleSelector(),
            const SizedBox(height: 24),

            CustomButton(text: 'Créer un compte', onPressed: _handleRegister, isLoading: _isRegisterLoading),
          ],
        ),
      ),
    );
  }

  // Widget de sélection du rôle
  Widget _buildRoleSelector() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Je suis un :',
          style: TextStyle(fontSize: 14, fontWeight: FontWeight.w500, color: Color(0xFF3C3C3C)),
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            Expanded(
              child: _RoleCard(
                icon: Icons.person,
                label: 'Client',
                description: 'Je veux payer des commerçants',
                isSelected: _selectedRole == 'client',
                onTap: () => setState(() => _selectedRole = 'client'),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: _RoleCard(
                icon: Icons.store,
                label: 'Commerçant',
                description: 'Je veux recevoir des paiements',
                isSelected: _selectedRole == 'commercant',
                onTap: () => setState(() => _selectedRole = 'commercant'),
              ),
            ),
          ],
        ),
      ],
    );
  }
}

// ✅ Widget carte de rôle
class _RoleCard extends StatelessWidget {
  final IconData icon;
  final String label;
  final String description;
  final bool isSelected;
  final VoidCallback onTap;

  const _RoleCard({
    required this.icon,
    required this.label,
    required this.description,
    required this.isSelected,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    const selectedColor = Color(0xFFFF6B6B);

    return GestureDetector(
      onTap: onTap,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 12),
        decoration: BoxDecoration(
          color: isSelected ? selectedColor.withOpacity(0.06) : Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: isSelected ? selectedColor : Colors.grey.shade300,
            width: isSelected ? 2 : 1,
          ),
        ),
        child: Column(
          children: [
            // Icône
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: isSelected ? selectedColor.withOpacity(0.12) : Colors.grey.shade100,
                shape: BoxShape.circle,
              ),
              child: Icon(
                icon,
                size: 28,
                color: isSelected ? selectedColor : Colors.grey.shade600,
              ),
            ),
            const SizedBox(height: 10),

            // Label
            Text(
              label,
              style: TextStyle(
                fontSize: 15,
                fontWeight: FontWeight.w700,
                color: isSelected ? selectedColor : const Color(0xFF3C3C3C),
              ),
            ),
            const SizedBox(height: 4),

            // Description
            Text(
              description,
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 11, color: Colors.grey.shade600, height: 1.3),
            ),
            const SizedBox(height: 10),

            // Radio indicator
            AnimatedContainer(
              duration: const Duration(milliseconds: 200),
              width: 22,
              height: 22,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(
                  color: isSelected ? selectedColor : Colors.grey.shade400,
                  width: 2,
                ),
                color: isSelected ? selectedColor : Colors.transparent,
              ),
              child: isSelected
                  ? const Icon(Icons.check, size: 13, color: Colors.white)
                  : null,
            ),
          ],
        ),
      ),
    );
  }
}