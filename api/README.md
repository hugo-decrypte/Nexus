# API Nexus (PHP)

## Objectif

Exposer les endpoints REST du système de monnaie virtuelle : authentification (JWT), comptes, transactions, rechargements, journaux, administration, etc. L’application suit une structure en couches (domaine, cas d’usage, infrastructure, couche HTTP Slim).

## Structure (aperçu)

```
api/
├── app/
│   ├── composer.json
│   ├── config/
│   ├── public/              # Point d’entrée web (index.php)
│   └── src/
│       ├── api/             # Actions, routes, middlewares, DTOs
│       └── application_core/
│           ├── application/ # Services et cas d’usage
│           ├── domain/      # Entités
│           └── exceptions/
└── logs/
```

## Exécution avec Docker

À la racine du dépôt Nexus :

```bash
docker compose up -d api.nexus
```

Le service installe les dépendances Composer au démarrage et lance le serveur PHP intégré sur le port 80 du conteneur (mappé sur `6080` par défaut sur l’hôte).

## Exécution locale (sans Docker)

Depuis `api/app` :

```bash
composer install
php -S 0.0.0.0:8080 -t public
```

Configurez la connexion à PostgreSQL et les variables d’environnement attendues par l’application (fichiers dans `config/` / `.env` selon votre setup).

## Commandes utiles

```bash
# Logs du service API
docker compose logs -f api.nexus

# Shell dans le conteneur API
docker compose exec api.nexus sh
```

Pour le démarrage complet de la stack (base, mail, front), voir le [README à la racine](../README.md) et [PREPARATION.md](../PREPARATION.md).
