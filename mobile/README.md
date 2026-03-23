# Application mobile Nexus (Flutter)

Client **Flutter** du projet **Nexus** : authentification, accueil, paiement (envoi / réception avec QR), rechargement, historique des transactions. Elle communique avec la même API REST que le front web.

## Prérequis

- **Flutter SDK** compatible avec `environment.sdk: ^3.7.2` du `pubspec.yaml`
- Un **appareil** ou **émulateur** (Android, iOS, etc.)
- L’**API** accessible depuis la machine ou le réseau du téléphone (URL à configurer, voir ci-dessous)

## Structure du projet

```
mobile/
├── lib/
│   ├── main.dart
│   ├── domain/
│   │   └── modeles/          # Modèles (transaction, paiement…)
│   └── presentation/
│       ├── screens/          # Splash, auth, home, paiement, historique, rechargement…
│       └── widgets/          # Barre d’app, navigation, composants auth
├── assets/
│   └── images/
├── pubspec.yaml
└── android/ / ios/ / …       # Projets plateforme générés / natifs
```

## Dépendances principales

| Paquet | Usage |
|--------|--------|
| `http` | Appels REST vers l’API |
| `qr_flutter` | Affichage de QR codes |
| `mobile_scanner` | Scan de QR codes |
| `permission_handler` | Permissions caméra, etc. |
| `shared_preferences` | Stockage local (token, infos utilisateur) |

## Installation et lancement

```bash
cd mobile
flutter pub get
flutter run
```

Choisissez un appareil connecté ou un émulateur quand Flutter le demande.

### Configurer l’URL de l’API

La constante **`baseUrl`** est dupliquée dans trois fichiers : mettez la **même** valeur partout pour un environnement donné.

- `lib/presentation/screens/auth/services/auth_service.dart`
- `lib/presentation/screens/paiement/service/payment_service.dart`
- `lib/presentation/screens/historique/service/transaction_service.dart`

(Idéalement, centraliser l’URL dans une seule config partagée évite les oublis.)

- **Émulateur Android** : souvent `http://10.0.2.2:6080` pour joindre `localhost:6080` sur la machine hôte.
- **Appareil physique** : utilisez l’**adresse IP locale** de votre PC (même Wi‑Fi) et le port exposé par l’API (ex. `6080` si vous utilisez `docker compose` comme dans le README racine).
- **Serveur distant** : remplacez par l’URL complète du déploiement (schéma `http` ou `https` selon le cas).

Pensez à aligner `baseUrl` (et les autres services qui appellent l’API) sur le même hôte pour éviter les mélanges entre environnements.

## Build release (rappel)

```bash
# Android (APK / App Bundle selon votre config)
flutter build apk
# ou
flutter build appbundle

# iOS (macOS + Xcode)
flutter build ios
```

## Tests et qualité

```bash
flutter test
flutter analyze
```

## Liens utiles

- [README du dépôt Nexus](../README.md)
- [Préparation / Docker](../PREPARATION.md)
- [Documentation Flutter](https://docs.flutter.dev/)
