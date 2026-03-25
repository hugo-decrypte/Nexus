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