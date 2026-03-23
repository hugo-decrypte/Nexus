# Préparation — environnement Nexus

## Avant de lancer le projet

1. **Installer Docker** (Docker Desktop ou moteur + plugin Compose) et vérifier :
   ```bash
   docker --version
   docker compose version
   ```

2. **Configurer PostgreSQL** : le fichier `nexus.env` à la racine du dépôt définit `POSTGRES_DB`, `POSTGRES_USER` et `POSTGRES_PASSWORD`. Adaptez-les si besoin avant le premier `docker compose up` (les scripts dans `init-db/` s’exécutent à l’initialisation du volume).

3. **Se placer à la racine du dépôt** :
   ```bash
   cd Nexus
   ```

4. **Construire et démarrer les services** :
   ```bash
   docker compose up -d --build
   ```

5. **Vérifier que les conteneurs tournent** :
   ```bash
   docker compose ps
   ```

---

## Développement sans tout reconstruire

### Frontend (`web/public`)

```bash
cd web/public
npm install
npm run dev
```

### API (`api/app`)

Avec PHP et Composer installés localement, depuis `api/app` :

```bash
composer install
php -S 0.0.0.0:8080 -t public
```

### Mobile (`mobile`)

```bash
cd mobile
flutter pub get
flutter run
```

---

## Commandes utiles pour le dépannage

### État et logs

```bash
docker compose ps
docker compose logs
docker compose logs api.nexus
docker compose logs web.nexus
docker compose logs nexus.db
```

### Accéder à un conteneur

```bash
docker compose exec api.nexus sh
docker compose exec nexus.db psql -U nexus -d nexus
```

(Remplacez `nexus` / `nexus` par les valeurs de `nexus.env` si vous les avez modifiées.)

### Repartir de zéro (données DB perdues)

```bash
docker compose down -v
docker compose up -d --build
```

**Attention** : `-v` supprime les volumes, donc la base PostgreSQL est réinitialisée.

### Nettoyage ponctuel des conteneurs

```bash
docker compose down
```

---
