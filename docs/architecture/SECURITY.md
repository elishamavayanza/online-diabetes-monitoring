# Sécurité — JWT, rôles et permissions

Ce document décrit le modèle de sécurité réellement implémenté dans le code (`src/Security/`, `config/packages/security.yaml`, `config/packages/lexik_jwt_authentication.yaml`).

Complément : la matrice détaillée des capacités métier par rôle est décrite dans [ROLE_CAPABILITIES.md](./ROLE_CAPABILITIES.md).

---

## 1. Authentification par JWT (Lexik)

- **Provider** : `app_user_provider`, entité `App\Entity\Identity\User`, propriété d'identification **email**.
- **Firewalls** (première règle correspondante appliquée) :

| Firewall | Pattern | Configuration |
|---|---|---|
| `dev` | `^/(_profiler\|_wdt\|assets\|build)/` | `security: false` |
| `login` | `^/api/login_check` | stateless — **sans** `json_login` : la connexion est gérée manuellement par `AuthController::login` (vérification `UserPasswordHasherInterface` puis génération du token via `JWTTokenManagerInterface`) |
| `api` | `^/api` | stateless, `jwt: ~` — chaque requête `/api/*` est authentifiée par le JWT Bearer |

- **access_control** :

| Règle | Exigence |
|---|---|
| `^/api/login` | `PUBLIC_ACCESS` (connexion, mot de passe oublié) |
| `^/api/doc` | `PUBLIC_ACCESS` (Swagger UI / JSON) |
| `^/api` | `IS_AUTHENTICATED_FULLY` (tout le reste) |

- **Hashing des mots de passe** : `PasswordAuthenticatedUserInterface` → algorithme `auto` (bcrypt/argon2 choisis automatiquement par Symfony). En environnement test, le coût est réduit pour accélérer les tests.
- **Clés JWT** : référencées par variables d'environnement (`JWT_SECRET_KEY`, `JWT_PUBLIC_KEY`, `JWT_PASSPHRASE`) dans `config/packages/lexik_jwt_authentication.yaml`. Aucune valeur en clair dans la configuration.
- **Claims personnalisés** : à la création d'un JWT, `JWTCreatedSubscriber` ajoute `fullname` et `roles` (emplacement réservé pour un futur claim `permissions`).

---

## 2. Rôles (enum `App\Entity\Identity\Role`)

| Rôle | Porteur |
|---|---|
| `ROLE_ROOT` | Super administrateur (`Administrator`) |
| `ROLE_ADMIN` | Administrateur d'organisation (attribué à un `Administrator`) |
| `ROLE_CLINICIAN` | Cliniciens (`HealthcareProfessional` de type `CLINICIAN`) |
| `ROLE_NUTRITIONIST` | Nutritionnistes (`HealthcareProfessional` de type `NUTRITIONIST`) |
| `ROLE_PATIENT` | Patients |

---

## 3. Permissions (enum `App\Security\SecurityAction`)

63 valeurs regroupées par thème :

| Thème | Valeurs |
|---|---|
| Général | `VIEW`, `CREATE`, `UPDATE`, `DELETE` |
| Organisation | `MANAGE_ORGANIZATION`, `MANAGE_FACILITY`, `MANAGE_DEPARTMENT` |
| Utilisateurs | `MANAGE_USERS`, `SUSPEND_USER`, `ACTIVATE_USER` |
| Patients | `VIEW_PATIENT`, `UPDATE_PATIENT`, `TRANSFER_PATIENT`, `ARCHIVE_PATIENT`, `ACTIVATE_PATIENT` |
| Dossier médical | `VIEW_MEDICAL_RECORD`, `CREATE_MEDICAL_RECORD`, `CREATE_DIAGNOSIS`, `UPDATE_DIAGNOSIS`, `CREATE_MEDICAL_NOTE` |
| Mesures | `RECORD_GLUCOSE`, `RECORD_BLOOD_PRESSURE`, `RECORD_HBA1C`, `RECORD_WEIGHT`, `RECORD_ACTIVITY`, `VIEW_MEASUREMENTS` |
| Laboratoire | `VIEW_LABORATORY_RESULT`, `UPLOAD_LABORATORY_RESULT` |
| Traitement | `VIEW_PRESCRIPTION`, `CREATE_PRESCRIPTION`, `UPDATE_PRESCRIPTION`, `CANCEL_PRESCRIPTION`, `VALIDATE_PRESCRIPTION`, `RECORD_MEDICATION_INTAKE`, `MANAGE_MEDICATION` |
| Nutrition | `MANAGE_FOOD`, `MANAGE_FOOD_CATEGORY`, `MANAGE_MEAL`, `VIEW_NUTRITION`, `CREATE_NUTRITION_ADVICE` |
| Rendez-vous | `VIEW_APPOINTMENT`, `CREATE_APPOINTMENT`, `UPDATE_APPOINTMENT`, `CANCEL_APPOINTMENT`, `CONFIRM_APPOINTMENT`, `REQUEST_RESCHEDULE` |
| Communication | `SEND_MESSAGE`, `READ_MESSAGE`, `DOWNLOAD_ATTACHMENT` |
| Notifications | `VIEW_NOTIFICATION`, `CREATE_NOTIFICATION`, `MARK_NOTIFICATION_READ` |
| Audit | `VIEW_AUDIT_LOG`, `VIEW_DATA_ACCESS_LOG`, `CREATE_DATA_ACCESS_LOG` |
| Administration | `MANAGE_ROLES`, `MANAGE_PERMISSIONS` |

---

## 4. `SecurityService` (RBAC + multi-tenant)

Implémentation (`final`) de `SecurityServiceInterface` dans `src/Security/SecurityService.php`. Injecte la façade `Symfony\Bundle\SecurityBundle\Security`.

### 4.1 Utilisateur courant

- `getCurrentUser()` : renvoie l'utilisateur connecté, lève `AccessDeniedException` si non authentifié.
- `isAuthenticated()` : booléen.

### 4.2 Rôles

- `isSuperAdmin()` ↔ `ROLE_ROOT`
- `isOrganizationAdmin()` ↔ `ROLE_ADMIN`
- `isClinician()` ↔ `ROLE_CLINICIAN`
- `isNutritionist()` ↔ `ROLE_NUTRITIONIST`
- `isPatient()` ↔ `ROLE_PATIENT`
- `hasRole()`, `hasAnyRole()`

### 4.3 Accès organisation (`checkOrganizationAccess`)

Pipeline séquentiel :

1. Super Admin → accès global (sans vérification) ;
2. L'organisation doit être **active** ;
3. **Multi-tenant** : l'utilisateur doit avoir un **membership actif** dans l'organisation (`belongsToOrganization`) ;
4. Puis délégation par rôle à une liste blanche d'actions autorisées (`checkOrganizationAdminAction` / `checkClinicianAction` / `checkNutritionistAction` / `checkPatientAction`), sinon **refus par défaut** (`denyIfNotAllowed`).

### 4.4 Accès aux données patient (`checkPatientAccess`)

- **Super Admin** → accès total ;
- **Patient** → uniquement ses **propres** données (`isPatientOwner`) ;
- **Admin d'organisation** → doit appartenir à l'organisation du patient (premier membership actif trouvé) ;
- **Clinicien / Nutritionniste** → doivent être **affectés au patient** (`isAssignedToPatient`, parcourt les `CareTeamAssignment` actifs) **et** disposer de la permission ;
- Sinon → refus.

### 4.5 Accès professionnel (`checkProfessionalAccess`)

Autorise Super Admin, Clinicien et Nutritionniste ; sinon refus.

### 4.6 Permissions (`hasPermission` / `checkPermission`)

- Super Admin → toujours `true` ;
- Sinon délégation à `$this->security->isGranted($permission)`.
- Préparé pour brancher un futur système `RolePermission` / `UserPermission` (cf. `ROLE_CAPABILITIES.md`).

### 4.7 Vérifications combinées

- `checkOrganizationAccessAndActive()` : enchaîne accès + vérification d'activité.
- `checkPatientAccessAndOrganization()` : retrouve l'organisation du patient, vérifie accès organisation + activité, puis accès patient.

---

## 5. Matrice des actions par rôle (résumé des listes blanches)

| Domaine | Admin organisation | Clinicien | Nutritionniste | Patient |
|---|---|---|---|---|
| Organisation | gestion org/établissements/départements | — | — | — |
| Utilisateurs | créer/suspendre/activer | — | — | — |
| Patients | voir/mettre à jour/transférer/archiver/activer | voir patients affectés | voir patients affectés | modifier son profil |
| Dossier médical | voir | complet (voir/créer) + diagnostics + notes | — | — |
| Mesures | — | toutes | glucose + voir mesures/nutrition | enregistrer glucose/poids/tension/activité, voir mesures |
| Laboratoire | — | voir/upload | — | — |
| Prescriptions | — | créer/modifier/annuler/valider | — | voir + prises |
| Médicaments | — | gérer catalogue | — | — |
| Nutrition | voir | — | gérer aliments/catégories/repas/conseils | voir repas/calories |
| Rendez-vous | voir/replanifier/annuler | voir/créer | voir | voir/confirmer/reporter |
| Communication | messagerie | messagerie + pièces jointes | messagerie + pièces jointes | messagerie + pièces jointes |
| Notifications | — | — | — | voir + marquer lu |

> La matrice détaillée et exhaustive est fournie par le document d'origine [ROLE_CAPABILITIES.md](./ROLE_CAPABILITIES.md).

---

## 6. Gestion des erreurs liées à la sécurité

| Situation | Réponse |
|---|---|
| Non authentifié (token manquant/invalide/expiré) | `401 Unauthorized` (exception `AuthenticationException`) |
| Authentifié mais accès refusé (permission, appartenance, affectation) | `403 Forbidden` (exception `AccessDeniedException`) |
| Autres erreurs HTTP | normalisées par `HttpExceptionListener` (voir [README.md](./README.md#37-gestion-des-erreurs)) |

---

## 7. Commandes sécurité

| Commande | Rôle |
|---|---|
| `php bin/console app:security:create-root` | Crée ou met à jour le compte super administrateur `ROLE_ROOT`. En prod, le mot de passe est obligatoire (`--password`) ; sinon il est généré aléatoirement et affiché en fin d'exécution. Option `--email` (défaut `root@diabcare.com`). |
