# Architecture — Vue d'ensemble

## 1. Vue globale

**DiabCare** est une API REST Symfony organisée en **domaines métier** et en **couches techniques**. Le projet adopte une architecture orientée services : les contrôleurs restent légers, la logique métier est portée par les services, et la conversion entre les DTO (entrées/sorties) et les entités Doctrine est assurée par des mappers dédiés.

```text
src/
├── Command/              # Commandes console (ex : création du compte root)
├── Controller/
│   └── Api/              # Contrôleurs REST (36 fichiers, 11 domaines)
├── DTO/
│   ├── Request/          # DTO de requête (immutables, validés, documentés OpenAPI)
│   ├── Response/         # DTO de réponse (immutables, factory fromEntity())
│   └── Feedback.php      # Wrapper universel de réponse des services
├── Entity/               # Entités Doctrine + enums PHP 8 (par domaine)
├── EventListener/        # Normalisation des erreurs HTTP (kernel.exception)
├── EventSubscriber/      # Enrichissement du payload JWT
├── Mapper/               # Conversion DTO ↔ Entité (36 mappers)
├── Repository/           # Accès aux données (Doctrine)
├── Security/             # Service de sécurité / RBAC
├── Service/              # Logique métier (37 services)
└── Kernel.php
```

## 2. Les domaines métier

| Domaine | Dossier | Description |
|---|---|---|
| Identité & comptes | `Controller/Api/Identity`, `Entity/Identity` | Patients, professionnels de santé, administrateurs, rôles |
| Organisations de santé | `Healthcare` | Organisations, établissements, départements, adhésions, équipes de soins |
| Rendez-vous | `Appointment` | Rendez-vous médicaux et rappels associés |
| Suivi médical | `Medical` | Glycémie, tension, HbA1c, poids/IMC, activité, résultats de labo, diagnostics, dossiers et notes médicaux |
| Traitements | `Treatment` | Médicaments, prescriptions, lignes de prescription, versions, prises de médicaments |
| Nutrition | `Nutrition` | Catégories d'aliments, aliments, repas et éléments de repas |
| Communication | `Communication` | Conversations, messages, pièces jointes, accusés de lecture |
| Notifications | `Notification` | Notifications système et règles de rappel (CRON) |
| Patient | `Patient` | Allergies, contacts d'urgence, consentements médicaux |
| Commun | `Common` | Pièces jointes génériques, entités de base |

## 3. Les couches techniques

### 3.1 Couche Contrôleur (`src/Controller/Api/`)

- Un contrôleur par ressource, routes déclarées par **attributs** `#[Route]`.
- Pattern dominant : action unique `create` recevant un **DTO Request** via `#[MapRequestPayload]` (désérialisation + validation automatique), délégation au service, réponse JSON du `Feedback`.
- Réponses standard : `201` (création), `200` (lecture), `400` (validation/erreur métier), `401` (non authentifié), `403` (accès refusé), `404` (introuvable), `422` (violations de contraintes).
- La protection globale est assurée par le firewall (JWT) — voir [SECURITY.md](./SECURITY.md).

### 3.2 Couche DTO (`src/DTO/`)

- **RequestDTO** : classes `readonly`, contraintes de validation Symfony (`#[Assert\...]`), attributs OpenAPI (`#[OA\Property]`) pour la doc Swagger. Un par ressource.
- **ResponseDTO** : classes `readonly`, factory statique `fromEntity(Entity): self`. Un par ressource.
- **`Feedback`** : wrapper sérialisé en JSON `{ flush, flushDescription, status, errors, warnings, data }`, retourné par tous les services. Il centralise le statut HTTP, les erreurs, les avertissements et les données de réponse.

### 3.3 Couche Service (`src/Service/`)

- Une classe par ressource, injectée par autowiring (propriétés `readonly`).
- Chaque service : vérification des permissions → chargement des entités liées → contrôle d'accès patient/organisation → mappage → persistance → réponse.
- Retour systématique d'un objet `Feedback`.
- Détail complet dans [SERVICES.md](./SERVICES.md).

### 3.4 Couche Mapper (`src/Mapper/`)

- Une classe par ressource, miroir exact des services.
- Deux méthodes principales :
  - `mapRequestToEntity(RequestDTO, parents..., ?Entity $existing = null): Entity`
  - `mapEntityToResponse(Entity): ResponseDTO` (délègue à `ResponseDTO::fromEntity()`)

### 3.5 Couche Repository (`src/Repository/`)

- Un repository par entité, héritant de `ServiceEntityRepository`.
- Fournit les requêtes d'accès aux données utilisées par les services.

### 3.6 Sécurité (`src/Security/`)

- `SecurityService` (implémente `SecurityServiceInterface`) : RBAC, contrôle d'accès aux organisations et aux données patients, multi-tenant.
- `SecurityAction` : enum listant les 63 permissions/actions du système.
- Détail dans [SECURITY.md](./SECURITY.md).

### 3.7 Gestion des erreurs (`src/EventListener/`, `src/EventSubscriber/`)

- `ConstraintViolationExceptionListener` (priorité 10) : transforme les violations du Validator en réponse **422** avec le détail champ → message.
- `HttpExceptionListener` (priorité 0) : filet de sécurité qui normalise toute exception HTTP en JSON (`HttpErrorResponsePayload`). `403` pour les accès refusés, `401` pour les erreurs d'authentification, `500` sinon. Les détails techniques ne sont exposés qu'en environnement dev/test.
- `JWTCreatedSubscriber` : à la création d'un JWT, ajoute les claims `fullname` et `roles`.

## 4. Cycle de vie d'une requête type

```text
Client ── HTTP POST /api/ressource (Bearer JWT)
   │
   ▼
[firewall api] ── authentification JWT stateless
   │
   ▼
[Contrôleur] ── désérialise + valide le DTO Request (#[MapRequestPayload])
   │              └─ échec → 422 (ConstraintViolationExceptionListener)
   ▼
[Service] ── vérifie permissions (SecurityService) → 403 si refus
   │         ── contrôle d'accès patient/organisation
   │         ── mappe DTO → Entité (Mapper)
   │         ── persiste + flush
   │         ── mappe Entité → ResponseDTO
   ▼
[Feedback] ── statut + données sérialisés en JSON
   │
   ▼
Client
```

## 5. Technologies

| Brique | Technologie |
|---|---|
| Langage | PHP >= 8.2 |
| Framework | Symfony 7.4 |
| ORM | Doctrine ORM 3.x + Doctrine Migrations 4.x |
| Base de données | MariaDB (configuré dans `DATABASE_URL`) |
| Authentification | Lexik JWT Authentication Bundle 3.x |
| Documentation API | NelmioApiDocBundle 5.x (OpenAPI / Swagger) |
| Frontend | React 19 + TypeScript 5.9 (Webpack Encore 7) |
| Conteneurisation | Docker Compose (PostgreSQL + Mailpit en dev) |

## 6. Fichiers de configuration clés

| Fichier | Rôle |
|---|---|
| `config/packages/security.yaml` | Firewalls, provider, hashing, access_control |
| `config/packages/lexik_jwt_authentication.yaml` | Clés JWT (variables d'environnement) |
| `config/packages/nelmio_api_doc.yaml` | Documentation OpenAPI |
| `config/services.yaml` | Autowiring global de `src/` |
| `config/routes.yaml` | Import automatique des contrôleurs |
| `compose.yaml` / `compose.override.yaml` | Services Docker (DB, mailer dev) |

## 7. Points d'attention

- Le périmètre CRUD complet n'est aujourd'hui implémenté que pour deux ressources (`HealthcareOrganization` et `HealthcareProfessional`) ; la majorité des services exposent uniquement l'action `create`.
- Des anomalies ont été relevées lors de la documentation (appels `$feedback->hasError()` vs `hasErrors()`, propriété non injectée dans `MessageReadReceiptService`, incohérences de migrations). Elles sont répertoriées dans [../known-issues.md](../known-issues.md).
