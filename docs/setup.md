# Installation et configuration

Guide d'installation et de configuration du backend **DiabCare API**.

## 1. Prérequis

- PHP **>= 8.2** (extensions `ctype`, `iconv`)
- Composer
- MariaDB (ou MySQL) — c'est le SGBD effectivement utilisé par `DATABASE_URL` et par les migrations
- Node.js + npm (pour le frontend React/TypeScript)
- Facultatif : Docker Compose, Symfony CLI

## 2. Installation

```bash
# Cloner le dépôt
git clone <repository-url>
cd diabcare-api-v7

# Dépendances PHP
composer install

# Dépendances frontend (Webpack Encore)
npm install
```

## 3. Configuration de l'environnement

Copier `.env` vers `.env.local` puis adapter les valeurs :

```bash
cp .env .env.local
```

### Variables d'environnement utilisées

| Variable | Rôle |
|---|---|
| `APP_ENV` | Environnement (`dev`, `prod`, `test`) |
| `APP_SECRET` | Secret d'application Symfony |
| `APP_SHARE_DIR` | Répertoire de partage de l'application |
| `DEFAULT_URI` | URI par défaut de l'application |
| `DATABASE_URL` | DSN de connexion à la base de données (MariaDB) |
| `JWT_SECRET_KEY` | Chemin vers la clé privée JWT |
| `JWT_PUBLIC_KEY` | Chemin vers la clé publique JWT |
| `JWT_PASSPHRASE` | Passphrase des clés JWT |
| `MAILER_DSN` | DSN du serveur d'envoi d'emails |

> Les clés JWT sont stockées dans `config/jwt/` (fichiers `private.pem` / `public.pem`) et référencées par variables d'environnement. Elles ne doivent jamais être commitées dans un dépôt partagé.

## 4. Base de données

```bash
# Créer la base
php bin/console doctrine:database:create

# Appliquer les migrations
php bin/console doctrine:migrations:migrate
```

> **Attention** : à l'état actuel du dépôt, l'exécution de l'ensemble des migrations provoque un conflit entre V1 et V2 sur les tables `communication_*`. Voir [known-issues.md](./known-issues.md).

## 5. Compte super administrateur

```bash
php bin/console app:security:create-root
```

Par défaut : email `root@diabcare.com`. En production, fournir un mot de passe :

```bash
php bin/console app:security:create-root --email=admin@diabcare.com --password='S3cure!Pwd'
```

## 6. Lancer l'application

```bash
# Avec la Symfony CLI
symfony server:start

# Ou avec le serveur PHP intégré
php -S 127.0.0.1:8000 -t public
```

- Racine de l'API : `GET /` → `{ message, version: "v1", documentation: "/api/doc" }`
- Documentation Swagger : `http://127.0.0.1:8000/api/doc`
- Documentation OpenAPI JSON : `http://127.0.0.1:8000/api/doc.json`

## 7. Frontend (Webpack Encore)

```bash
npm run dev      # build de développement (watch)
npm run build    # build de production
```

Les fichiers sont compilés dans `public/build/`.

## 8. Outillage Docker

Le dépôt contient une configuration Docker Compose **destinée au développement** :

- **`compose.yaml`** : service `database` — image `postgres:16-alpine` (PostgreSQL) avec volume persistant `database_data`.
- **`compose.override.yaml`** (dev) : expose le port `5432` et ajoute un service `mailer` — image `axllent/mailpit` (SMTP sur `1025`, interface web sur `8025`).

> **Incohérence à noter** : l'application est configurée et développée pour **MariaDB**, alors que le service Docker `database` est basé sur **PostgreSQL**. Le conteneur Docker n'est donc pas directement compatible avec le `DATABASE_URL` actuel. Voir [known-issues.md](./known-issues.md).

## 9. Tests

```bash
php bin/phpunit
```

> **Note** : aucun répertoire `tests/` n'est actuellement présent dans le dépôt.
