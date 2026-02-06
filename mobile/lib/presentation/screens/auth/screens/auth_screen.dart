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
  //tab Connexion/Inscription
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

    if (!_loginFormKey.currentState!.validate()) {
      return;
    }

    setState(() {
      _isLoginLoading = true;
    });

    try {
      await AuthService.login(
        email: _loginEmailController.text.trim(),
        password: _loginPasswordController.text,
      );

      if (!mounted) return;

      // Naviguer vers l'accueil
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const HomeScreen()),
      );
    } catch (e) {
      setState(() {
        _loginError = e.toString().replaceAll('Exception: ', '');
      });
    } finally {
      if (mounted) {
        setState(() {
          _isLoginLoading = false;
        });
      }
    }
  }

  Future<void> _handleRegister() async {
    setState(() {
      _registerError = null;
      _successMessage = null;
    });

    if (!_registerFormKey.currentState!.validate()) {
      return;
    }

    setState(() {
      _isRegisterLoading = true;
    });

    try {
      await AuthService.register(
        nom: _registerNameController.text.trim(),
        prenom: _registerPrenomController.text.trim(),
        email: _registerEmailController.text.trim(),
        password: _registerPasswordController.text,
      );

      if (!mounted) return;

      // Succès
      setState(() {
        _successMessage = 'Compte créé avec succès ! Vous pouvez vous connecter.';
      });

      // Basculer vers l'onglet login après 1 seconde
      await Future.delayed(const Duration(seconds: 1));

      if (!mounted) return;

      _tabController.animateTo(0);
      _loginEmailController.text = _registerEmailController.text;

    } catch (e) {
      if (mounted) {
        setState(() {
          _registerError = e.toString().replaceAll('Exception: ', '');
        });
      }
    } finally {
      if (mounted) {
        setState(() {
          _isRegisterLoading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: Column(
          children: [
            // Header
            _buildHeader(),

            // Tab Bar
            _buildTabBar(),

            // Tab Views
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
          // Logo
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
            child: const Icon(
              Icons.account_balance_wallet,
              size: 48,
              color: Colors.white,
            ),
          ),
          const SizedBox(height: 20),
          const Text(
            'NEXUS',
            style: TextStyle(
              fontSize: 32,
              fontWeight: FontWeight.bold,
              color: Color(0xFFFF6B6B),
            ),
          ),
          const SizedBox(height: 8),
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
        indicator: BoxDecoration(
          color: const Color(0xFFFF6B6B),
          borderRadius: BorderRadius.circular(12),
        ),
        labelColor: Colors.white,
        unselectedLabelColor: const Color(0xFF3C3C3C),
        labelStyle: const TextStyle(
          fontSize: 15,
          fontWeight: FontWeight.w600,
        ),
        tabs: const [
          Tab(text: 'Connexion'),
          Tab(text: 'Inscription'),
        ],
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

            // Error message
            if (_loginError != null) ...[
              ErrorMessage(
                message: _loginError!,
                onDismiss: () {
                  setState(() {
                    _loginError = null;
                  });
                },
              ),
              const SizedBox(height: 20),
            ],

            // Success message
            if (_successMessage != null && _tabController.index == 0) ...[
              SuccessMessage(
                message: _successMessage!,
                onDismiss: () {
                  setState(() {
                    _successMessage = null;
                  });
                },
              ),
              const SizedBox(height: 20),
            ],

            // Email field
            CustomTextField(
              controller: _loginEmailController,
              label: 'Email',
              hintText: 'votre.email@exemple.com',
              prefixIcon: Icons.email_outlined,
              keyboardType: TextInputType.emailAddress,
              validator: FormValidators.validateEmail,
            ),

            const SizedBox(height: 20),

            // Password field
            CustomTextField(
              controller: _loginPasswordController,
              label: 'Mot de passe',
              hintText: '••••••••',
              prefixIcon: Icons.lock_outline,
              obscureText: !_showLoginPassword,
              validator: FormValidators.validatePassword,
              suffixIcon: IconButton(
                icon: Icon(
                  _showLoginPassword ? Icons.visibility_off : Icons.visibility,
                  color: Colors.black38,
                  size: 20,
                ),
                onPressed: () {
                  setState(() {
                    _showLoginPassword = !_showLoginPassword;
                  });
                },
              ),
            ),

            const SizedBox(height: 12),

            // Login button
            CustomButton(
              text: 'Se connecter',
              onPressed: _handleLogin,
              isLoading: _isLoginLoading,
            ),

            const SizedBox(height: 20),

            // Divider
            Row(
              children: [
                Expanded(child: Divider(color: Colors.grey.shade300)),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Text(
                    'ou',
                    style: TextStyle(
                      color: Colors.grey.shade600,
                      fontSize: 14,
                    ),
                  ),
                ),
                Expanded(child: Divider(color: Colors.grey.shade300)),
              ],
            ),

            const SizedBox(height: 20),

            // Social login buttons (optional)
            CustomButton(
              text: 'Continuer avec Google',
              onPressed: () {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Fonctionnalité à venir'),
                    duration: Duration(seconds: 2),
                  ),
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

            // Error message
            if (_registerError != null) ...[
              ErrorMessage(
                message: _registerError!,
                onDismiss: () {
                  setState(() {
                    _registerError = null;
                  });
                },
              ),
              const SizedBox(height: 20),
            ],

            // Prenom field
            CustomTextField(
              controller: _registerPrenomController,
              label: 'Prénom',
              hintText: 'Prénom',
              prefixIcon: Icons.person_2_outlined,
              validator: FormValidators.validateName,
            ),

            const SizedBox(height: 20),

            // Name field
            CustomTextField(
              controller: _registerNameController,
              label: 'Nom',
              hintText: 'Nom',
              prefixIcon: Icons.person_outline,
              validator: FormValidators.validateName,
            ),

            const SizedBox(height: 20),

            // Email field
            CustomTextField(
              controller: _registerEmailController,
              label: 'Email',
              hintText: 'email@mail.com',
              prefixIcon: Icons.email,
              keyboardType: TextInputType.emailAddress,
              validator: FormValidators.validateEmail,
            ),

            const SizedBox(height: 20),

            // Password field
            CustomTextField(
              controller: _registerPasswordController,
              label: 'Mot de passe',
              hintText: '••••••••',
              prefixIcon: Icons.lock_outline,
              obscureText: !_showRegisterPassword,
              validator: FormValidators.validatePassword,
              suffixIcon: IconButton(
                icon: Icon(
                  _showRegisterPassword ? Icons.visibility_off : Icons.visibility,
                  color: Colors.black38,
                  size: 20,
                ),
                onPressed: () {
                  setState(() {
                    _showRegisterPassword = !_showRegisterPassword;
                  });
                },
              ),
            ),

            const SizedBox(height: 20),

            // Confirm password field
            CustomTextField(
              controller: _registerValideMDPController,
              label: 'Confirmer le mot de passe',
              hintText: '••••••••',
              prefixIcon: Icons.lock_outline,
              obscureText: !_showConfirmPassword,
              validator: (value) => FormValidators.validateConfirmPassword(
                value,
                _registerPasswordController.text,
              ),
              suffixIcon: IconButton(
                icon: Icon(
                  _showConfirmPassword ? Icons.visibility_off : Icons.visibility,
                  color: Colors.black38,
                  size: 20,
                ),
                onPressed: () {
                  setState(() {
                    _showConfirmPassword = !_showConfirmPassword;
                  });
                },
              ),
            ),

            const SizedBox(height: 24),

            // Register button
            CustomButton(
              text: 'Créer un compte',
              onPressed: _handleRegister,
              isLoading: _isRegisterLoading,
            ),
          ],
        ),
      ),
    );
  }
}