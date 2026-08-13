# Anomalies documentées

Cette page répertorie, **à titre purement informatif**, les incohérences et points d'attention relevés lors de la documentation du dépôt. Aucune correction n'a été apportée au code source : ce document sert de référence pour les éventuels travaux futurs.

## 1. Migrations Doctrine

- **Conflit V1 / V2** : la migration `Version20260811145344_V2.php` recrée les tables `communication_conversations`, `communication_messages`, `communication_message_attachments`, `communication_message_read_receipts` déjà créées par `Version20260811142233_InitialSchemaV1.php`, avec un schéma divergent (V1 : table `conversation_participants` + `participant_id` ; V2 : `patient_id` + `user_id`). L'exécution de V1 puis V2 lèvera une erreur `table already exists`.
- **Migration vide** : `Version20260811142241_InitialSchemaV1.php` ne contient aucun SQL.

## 2. Couche Service

- **`MessageReadReceiptService`** (`src/Service/Communication/MessageReadReceiptService.php`) : la méthode `create()` utilise `$this->participantRepository`, mais le constructeur n'injecte que `$repository` (et non cette propriété). Cette propriété n'est jamais définie → erreur potentielle à l'exécution.
- **`FileAttachmentService::create()`** (`src/Service/Common/FileAttachmentService.php`) : construit un `$responseDTO` mais ne l'attache pas au `Feedback` (données non renvoyées dans la réponse).

## 3. Contrôleurs / Feedback

- Plusieurs contrôleurs appellent `$feedback->hasError()` (singulier) alors que `Feedback` définit la méthode `hasErrors()` → fatal error possible. Vérifier l'usage exact dans `src/Controller/Api/` (ex. routes d'accès aux fichiers).

## 4. Entités / Doctrine

- `MessageReadReceipt.message` déclare `inversedBy: 'readReceipts'`, mais la propriété inverse `Message::readReceipts` n'existe pas (couplage Doctrine incomplet).
- `Conversation.patient` est typé `User` (et non `Patient`).
- `Diagnosis.status` est une simple chaîne (`string 50`), pas un enum (contrairement au reste du modèle).
- Annotations `#[ORM\Table]` sans effet sur `PatientCommonOperation` (MappedSuperclass) et `Address` (Embeddable) : non utilisées par Doctrine, mais présentes dans le code.

## 5. Environnement / Infrastructures

- **PostgreSQL vs MariaDB** : le code, les migrations (`BIGINT UNSIGNED`, `utf8mb4`) et `DATABASE_URL` ciblent **MariaDB**, alors que `compose.yaml` fournit un service `database` basé sur **PostgreSQL** (`postgres:16-alpine`). Le conteneur Docker n'est donc pas utilisable tel quel.
- **README / CONTRIBUTING** : mentionnent PostgreSQL alors que le projet utilise MariaDB.
- **CONTRIBUTING.md** : décrit une structure `src/` avec des dossiers (`Enum/`, `Exception/`, `Trait/`, `Utils/`, `Validator/`) qui n'existent pas dans le dépôt.

## 6. Sécurité des données

- Des valeurs sensibles apparaissent en clair dans le dépôt : passphrase JWT, identifiants de base de données et `APP_SECRET` de développement (fichiers `.env` / `.env.dev`). Ces fichiers ne devraient pas être versionnés selon les règles de sécurité du projet (cf. `CONTRIBUTING.md`).

## 7. Périmètre fonctionnel

- Seules deux ressources disposent d'un CRUD complet (organisations de santé, professionnels). Toutes les autres n'exposent que l'action `create`.
- Routes d'authentification absentes : inscription (`register`), rafraîchissement de token (`refresh`), déconnexion (`logout`), vérification d'email, réinitialisation de mot de passe par token.
- `/api/forgot-password` est un **stub** (renvoie un message sans logique réelle) et, de surcroît, est protégé par le firewall `^/api` (nécessite donc un utilisateur authentifié, ce qui contredit la sémantique « mot de passe oublié »).
- Aucun `tests/` n'est présent alors que `CONTRIBUTING.md` prévoit l'exécution de `php bin/phpunit`.
