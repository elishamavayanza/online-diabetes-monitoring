# Contribuer à DiabCare API

Merci de l'intérêt que vous portez à **DiabCare API**.

Ce document décrit les règles de contribution, le processus de développement, les conventions de code et les bonnes pratiques adoptées par le projet.

Notre objectif est de maintenir une base de code propre, sécurisée, maintenable et évolutive tout en favorisant une collaboration efficace entre les contributeurs.

---

# 1. À propos du projet

**DiabCare API** est le backend de la plateforme **DiabCare**, une solution numérique conçue pour accompagner les professionnels de santé et les patients dans le suivi et la prise en charge du diabète.

L'API fournit des services REST sécurisés permettant notamment de gérer :

* l'authentification et l'autorisation des utilisateurs ;
* les patients ;
* les professionnels de santé (cliniciens et nutritionnistes) ;
* les structures de santé ;
* les prescriptions médicales ;
* les paramètres médicaux (glycémie, tension, poids, HbA1c, etc.) ;
* les conversations et la messagerie ;
* les événements médicaux et les rappels.

---

# 2. Technologies utilisées

Le projet repose sur les technologies suivantes :

* PHP 8.4 ou supérieur
* Symfony
* Doctrine ORM
* PostgreSQL
* Composer
* JWT Authentication
* NelmioApiDocBundle (OpenAPI / Swagger)
* PHPUnit

---

# 3. Workflow Git

Le projet suit une organisation inspirée de **Gitflow**.

## Branches principales

| Branche      | Description                                         |
| ------------ | --------------------------------------------------- |
| `main`       | Version stable destinée à la production             |
| `develop`    | Branche d'intégration des nouvelles fonctionnalités |
| `feature/*`  | Développement de nouvelles fonctionnalités          |
| `fix/*`      | Correction de bogues                                |
| `hotfix/*`   | Correctifs urgents                                  |
| `refactor/*` | Réorganisation interne du code                      |
| `docs/*`     | Documentation                                       |

### Règles de contribution

* Ne jamais effectuer de commit directement sur la branche **main**.
* Ne jamais pousser directement sur la branche **develop**.
* Toute modification doit être proposée via une **Pull Request**.
* Une Pull Request doit traiter un seul sujet ou une seule fonctionnalité.
* Les commits doivent être clairs, courts et explicites.

---

# 4. Convention des messages de commit

Format recommandé :

```text
[TAG] Type #Issue : Description
```

Exemples :

```text
[AUTH] Add #25 : Ajout de l'authentification JWT

[PATIENT] Fix #41 : Correction de la validation de la date de naissance

[API] Update #52 : Amélioration de la documentation des endpoints Patient

[DOC] Update #70 : Mise à jour du guide d'installation
```

### Tags disponibles

* AUTH
* USER
* PATIENT
* CLINICIAN
* NUTRITION
* HEALTH
* PRESCRIPTION
* MESSAGE
* EVENT
* API
* DB
* SECURITY
* TEST
* DOC

---

# 5. Structure du projet

```text
src/
├── Controller/
├── DTO/
├── Entity/
├── Enum/
├── Event/
├── Exception/
├── Mapper/
├── Repository/
├── Security/
├── Service/
├── Trait/
├── Utils/
└── Validator/
```

Chaque dossier possède une responsabilité précise afin de garantir une architecture claire et facilement maintenable.

---

# 6. Installation de l'environnement de développement

### Cloner le dépôt

```bash
git clone https://github.com/<organisation>/diabcare-api.git
cd diabcare-api
```

### Installer les dépendances

```bash
composer install
```

### Configurer l'environnement

```bash
cp .env .env.local
```

Configurez ensuite les paramètres de votre base de données dans le fichier `.env.local`.

### Créer la base de données

```bash
php bin/console doctrine:database:create
```

### Exécuter les migrations

```bash
php bin/console doctrine:migrations:migrate
```

### Lancer le serveur

```bash
symfony server:start
```

---

# 7. Conventions de développement

Merci de respecter les bonnes pratiques recommandées par Symfony.

* Utiliser l'injection de dépendances.
* Garder les contrôleurs simples et légers.
* Centraliser la logique métier dans les services.
* Valider systématiquement les données reçues.
* Utiliser des DTO pour les requêtes et les réponses.
* Écrire des services réutilisables.
* Respecter la norme PSR-12.
* Donner des noms explicites aux classes, méthodes et variables.

---

# 8. Base de données

Le projet utilise :

* Doctrine ORM
* PostgreSQL
* Doctrine Migrations

Toute modification du schéma de la base de données doit être accompagnée d'une migration.

Aucune modification ne doit être effectuée directement sur la base de données de production.

---

# 9. Conventions de l'API

L'API respecte les principes REST.

* utilisation correcte des méthodes HTTP (GET, POST, PUT, PATCH, DELETE) ;
* codes de réponse HTTP appropriés ;
* versionnement de l'API (`/api/v1`) ;
* validation des requêtes ;
* format uniforme des erreurs ;
* documentation OpenAPI (Swagger) pour chaque endpoint.

---

# 10. Tests

Avant de soumettre une Pull Request, exécutez les tests :

```bash
php bin/phpunit
```

Toute nouvelle fonctionnalité devrait être accompagnée de tests lorsque cela est possible.

---

# 11. Pull Requests

Avant d'ouvrir une Pull Request, vérifiez que :

* le code respecte les conventions du projet ;
* aucun fichier inutile n'est inclus ;
* tous les tests passent avec succès ;
* la documentation a été mise à jour si nécessaire ;
* les migrations de base de données sont présentes.

---

# 12. Signaler un problème

Utilisez les **GitHub Issues** pour signaler :

* un bogue ;
* une faille de sécurité ;
* une demande d'amélioration ;
* une erreur de documentation.

Merci de fournir le plus de détails possible afin de faciliter l'analyse.

---

# 13. Sécurité

Ne jamais versionner les fichiers ou informations sensibles :

* `.env.local`
* clés API
* mots de passe de base de données
* clés JWT
* configurations de production

Si vous découvrez une faille de sécurité, merci de la signaler de manière privée avant toute publication.

---

# 14. Revue de code

Chaque Pull Request sera examinée selon les critères suivants :

* qualité de l'architecture ;
* lisibilité du code ;
* performances ;
* sécurité ;
* maintenabilité ;
* documentation.

Les remarques de revue ont pour objectif d'améliorer la qualité globale du projet dans un esprit de collaboration.

---

# 15. Licence

En contribuant à **DiabCare API**, vous acceptez que vos contributions soient distribuées sous la licence du projet.
