# Nexus — Monnaie virtuelle (projet tutoré)

Projet de conception et de développement d’un système de paiement sécurisé inspiré des principes de la blockchain, destiné à des contextes ponctuels et localisés (festivals, marchés, campus, etc.). Ce dépôt regroupe l’application (API, interface web, client mobile) et la configuration Docker.

## Équipe

- Alexandre Cazottes  
- Arman Hayrapetyan  
- Bena Hugo  
- Tuline Leveque  
- Eloi Reignier  

---

## Documents et ressources

| Ressource | Lien |
|-----------|------|
| **CMS de suivi du projet** | [https://great-panini.163-5-143-4.plesk.page/](https://great-panini.163-5-143-4.plesk.page/) |
| **TD d’explication** | [Guenego_MonnaieVirtuelle.pdf](./Guenego_MonnaieVirtuelle.pdf) |
| **Analyse des besoins (livré projet tuteuré)** | [Projet Tuto - Analyse des besoins.pdf](./Projet%20Tuto%20-%20Analyse%20des%20besoins.pdf) |


---

## Avant de commencer

Consultez [PREPARATION.md](./PREPARATION.md) pour les prérequis, la configuration minimale (`nexus.env`, dépendances) et les commandes utiles au quotidien et au dépannage.

---

## Structure du dépôt

```
Nexus/
├── api/
│   └── app/                 # API PHP (Slim, Composer)
├── build/
│   └── 8.4-cli.Dockerfile   # Image PHP pour l’API
├── init-db/                 # Scripts SQL d’initialisation PostgreSQL
├── mobile/                  # Application Flutter
├── web/
│   ├── Dockerfile           # Build Vue (Vite) + nginx
│   ├── nginx.conf
│   └── public/              # Frontend Vue 3 + Vite
├── docker-compose.yml
├── nexus.env                # Variables PostgreSQL (à adapter)
└── README.md
```

---

## Modules

### API backend (PHP)

**Dossier** : [`api/`](./api/)

**Dépendances** : PHP 8.4 (via Docker), Composer, PostgreSQL

[Voir le README du module API](./api/README.md)

---

### Interface web (Vue 3 + Vite)

**Dossier** : [`web/public/`](./web/public/)

**Dépendances** : Node.js 20 (build), nginx (conteneur `web.nexus`)

Développement local : `npm install` puis `npm run dev` dans `web/public/`.  
Production : image Docker construite depuis [`web/Dockerfile`](./web/Dockerfile).

[Voir le README du frontend](./web/public/README.md)

---

### Application mobile (Flutter)

**Dossier** : [`mobile/`](./mobile/)

**Dépendances** : SDK Flutter

[Voir le README mobile](./mobile/README.md)

---

### Stack Docker (tout-en-un)

**Fichier** : [`docker-compose.yml`](./docker-compose.yml)

Services principaux :

| Service | Rôle | Port(s) hôte (défaut) |
|---------|------|------------------------|
| `web.nexus` | Frontend compilé servi par nginx | `5173` → 80 |
| `api.nexus` | API PHP (`php -S` + Composer) | `6080` → 80 |
| `nexus.db` | PostgreSQL | `5432` |
| `adminer` | Administration SQL | `8080` |
| `mailer` (Mailpit) | Capture des e-mails en dev | `8025` (UI), `1025` (SMTP) |

```bash
docker compose up -d --build
```

- Frontend : http://localhost:5173  
- API : http://localhost:6080  
- Adminer : http://localhost:8080  
- Mailpit : http://localhost:8025  

---