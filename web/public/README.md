# Interface web Nexus (Vue 3 + Vite)

Application front du projet **Nexus** : connexion, compte, envoi / réception de paiements, rechargement, historique, réglages et espace administrateur. Elle consomme l’API PHP via des appels HTTP (préfixe `/api` en développement).

## Prérequis

- **Node.js** : `^20.19.0` ou `>=22.12.0` (voir `package.json` → `engines`)
- L’**API** doit être joignable (voir [README racine](../../README.md) et [PREPARATION.md](../../PREPARATION.md))

## Structure du dossier

```
web/public/
├── index.html
├── vite.config.js          # Proxy /api → API (voir ci-dessous)
├── package.json
└── src/
    ├── main.js
    ├── App.vue
    ├── router/               # Vue Router, garde d’auth
    ├── views/                # Pages (home, login, admin, etc.)
    ├── components/
    ├── services/             # auth, transactions, compte, admin…
    └── css/
```

## Installation et développement local

```bash
cd web/public
npm install
npm run dev
```

Par défaut, Vite démarre sur **http://localhost:5173** (sauf configuration contraire).

### Appel de l’API en dev

Dans `vite.config.js`, les requêtes vers **`/api`** sont proxifiées vers l’URL définie par la variable d’environnement **`VITE_API_URL`**, ou par défaut **`http://localhost:6080`** (port habituel de `api.nexus` avec Docker).

Exemple sous PowerShell :

```powershell
$env:VITE_API_URL="http://localhost:6080"; npm run dev
```

Les services du front utilisent des chemins du type `/api/auth/login` : le proxy enlève le préfixe `/api` avant d’atteindre l’API.

## Build de production

```bash
npm run build
```

Le résultat est généré dans `dist/`. L’image Docker du dossier `web/` copie ce build et le sert avec **nginx** (voir [`../Dockerfile`](../Dockerfile) et [`../nginx.conf`](../nginx.conf)).

## Lint

```bash
npm run lint
```

(Oxlint + ESLint, corrections automatiques où c’est prévu.)