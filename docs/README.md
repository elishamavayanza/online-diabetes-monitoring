# Documentation DiabCare API

Ce dossier regroupe la documentation technique du backend **DiabCare** (API REST Symfony).

> **Note** : cette documentation est descriptive et n'altère en aucun cas le code source du projet. Elle reflète l'état du dépôt à la date de sa rédaction.

## Table des matières

| Document | Contenu |
|---|---|
| [architecture/README.md](./architecture/README.md) | Vue d'ensemble de l'architecture, des couches et des patterns du projet |
| [architecture/API_ENDPOINTS.md](./architecture/API_ENDPOINTS.md) | Référence complète de toutes les routes de l'API (méthodes, chemins, corps, accès) |
| [architecture/SECURITY.md](./architecture/SECURITY.md) | Modèle de sécurité : JWT, rôles, permissions (RBAC), contrôle d'accès |
| [architecture/SERVICES.md](./architecture/SERVICES.md) | Couche service, DTO, mappers, wrapper de réponse `Feedback` |
| [architecture/ROLE_CAPABILITIES.md](./architecture/ROLE_CAPABILITIES.md) | Matrice détaillée des capacités par rôle (document d'origine du projet) |
| [architecture/ACTIVITY_2.mmd](./architecture/ACTIVITY_2.mmd) | Diagramme de flux (Mermaid) — parcours Admin → Médecin → Patient |
| [architecture/ACTIVTY_DIA.mmd](./architecture/ACTIVTY_DIA.mmd) | Diagramme (Mermaid) — écosystème et données médicales |
| [database/SCHEMA.md](./database/SCHEMA.md) | Schéma de base de données : entités, tables, relations, enums |
| [database/MIGRATIONS.md](./database/MIGRATIONS.md) | Historique des migrations Doctrine |
| [setup.md](./setup.md) | Installation, configuration de l'environnement, outils Docker |
| [frontend.md](./frontend.md) | Socle frontend (React + TypeScript via Webpack Encore) |
| [UML/DiabCare.vpp](./UML/DiabCare.vpp) | Modèle UML (Visual Paradigm) du projet |

## Présentation rapide

**DiabCare** est une plateforme numérique de suivi des personnes vivant avec le diabète. Ce dépôt contient le **backend** : une API REST sécurisée développée avec **Symfony 7.4** / **PHP 8+**, **Doctrine ORM**, **MariaDB**, et **JWT** (Lexik).

- API documentée : Swagger / OpenAPI via NelmioApiDocBundle (accessible à `/api/doc`)
- Architecture orientée services avec séparation en domaines métier
- Contrôle d'accès par rôle (RBAC) avec multi-tenant par organisation de santé
- Frontend minimal (React + TypeScript) intégré via Webpack Encore

## Conventions du projet

- Langue du code et des messages : **français**
- Routes REST sous préfixe `/api`
- Pattern dominant : contrôleur → DTO Request (`#[MapRequestPayload]`) → service → `Feedback` → Response DTO
- Tables de base de données au format `<domaine>_<nom>` en snake_case
