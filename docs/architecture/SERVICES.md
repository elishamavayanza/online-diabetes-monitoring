# Couche Service, DTO et Mappers

## 1. Couche Service (`src/Service/`)

### 1.1 Conventions communes

- **37 classes** : 36 services de ressource + `Service/Security/PasswordManager`.
- Injection de dépendances par constructeur (propriétés `readonly` promoted), autowiring Symfony.
- Dépendances récurrentes : le `XxxRepository`, le `XxxMapper`, `EntityManagerInterface` et **`SecurityServiceInterface`** (contrôle RBAC avant chaque opération).
- Chaque méthode publique retourne un objet **`Feedback`**.
- Pipeline type d'une méthode `create()` :

```text
vérification de permission (SecurityService)
   → chargement des entités liées (repositories)
   → contrôle d'accès patient/organisation
   → mapper->mapRequestToEntity()
   → persist() + flush()
   → mapper->mapEntityToResponse()
   → Feedback rempli (autoInitFlush)
```

- Les exceptions `AccessDeniedException` et `\Exception` sont capturées et converties en Feedback d'erreur (HTTP 422).

### 1.2 Catalogue des services par module

#### Appointment

| Service | Méthodes | Rôle |
|---|---|---|
| `AppointmentService` | `create()` | Crée un rendez-vous (patient, professionnel, organisation, établissement optionnel). |
| `AppointmentReminderService` | `create()` | Programme un rappel de rendez-vous. |

#### Common

| Service | Méthodes | Rôle |
|---|---|---|
| `FileAttachmentService` | `create()`, `getById()` | Pièces jointes génériques. `create` requiert `UPLOAD_LABORATORY_RESULT`, `getById` requiert `DOWNLOAD_ATTACHMENT`. |

#### Communication

| Service | Méthodes | Rôle |
|---|---|---|
| `ConversationService` | `create()` | Crée une conversation (permission `SEND_MESSAGE`). |
| `MessageService` | `create()` | Envoie un message dans une conversation. |
| `MessageAttachmentService` | `create()` | Lie une pièce jointe à un message (permission `SEND_MESSAGE`). |
| `MessageReadReceiptService` | `create()` | Enregistre un accusé de lecture. |

#### Healthcare

| Service | Méthodes | Rôle |
|---|---|---|
| `HealthcareOrganizationService` | `getById()`, `getPaginated()`, `create()`, `update()`, `delete()`, `suspend()` | CRUD complet + pagination + suspension. |
| `HealthcareFacilityService` | `create()` | Crée un établissement (permission `MANAGE_FACILITY`). |
| `DepartmentService` | `create()` | Crée un département (permission `MANAGE_DEPARTMENT`). |
| `OrganizationMembershipService` | `create()` | Crée une adhésion user ↔ organisation (permission `MANAGE_ROLES`). |
| `CareTeamAssignmentService` | `create()` | Affecte un professionnel à un patient (permission `VIEW_PATIENT` + accès patient). |

#### Identity

| Service | Méthodes | Rôle |
|---|---|---|
| `HealthcareProfessionalService` | `getAll()`, `create()`, `getById()`, `update()`, `delete()` | CRUD des professionnels de santé (permissions `VIEW`, `MANAGE_USERS`…). |

#### Medical

| Service | Méthodes | Rôle |
|---|---|---|
| `BloodGlucoseMeasurementService` | `create()` | Mesure de glycémie. |
| `BloodPressureMeasurementService` | `create()` | Mesure de tension. |
| `HbA1cMeasurementService` | `create()` | Mesure d'HbA1c. |
| `WeightMeasurementService` | `create()` | Mesure de poids (IMC). |
| `PhysicalActivityMeasurementService` | `create()` | Mesure d'activité physique. |
| `LaboratoryResultService` | `create()` | Résultat de laboratoire (permission `VIEW_PATIENT`). |
| `DiagnosisService` | `create()` | Diagnostic (action `CREATE_DIAGNOSIS`). |
| `MedicalRecordService` | `create()` | Dossier médical (accès organisation + patient, `CREATE_MEDICAL_RECORD`). |
| `MedicalNoteService` | `create()` | Note médicale (accès patient + organisation, `CREATE_MEDICAL_NOTE`). |

Tous les services de mesure vérifient `checkPatientAccess` avant persistance.

#### Notification

| Service | Méthodes | Rôle |
|---|---|---|
| `NotificationService` | `create()` | Crée une notification (permission `CREATE_NOTIFICATION`). |
| `ReminderRuleService` | `create()` | Crée une règle de rappel (permission `CREATE_NOTIFICATION`). |

#### Nutrition

| Service | Méthodes | Rôle |
|---|---|---|
| `FoodCategoryService` | `create()` | Catégorie d'aliment (`checkProfessionalAccess`, `MANAGE_FOOD_CATEGORY`). |
| `FoodService` | `create()` | Aliment (`MANAGE_FOOD`). |
| `MealService` | `create()` | Repas (`MANAGE_MEAL`). |
| `MealItemService` | `create()` | Élément de repas (`MANAGE_MEAL`). |

#### Patient

| Service | Méthodes | Rôle |
|---|---|---|
| `AllergyService` | `create()` | Allergie (permission `VIEW_MEDICAL_RECORD` + accès patient). |
| `EmergencyContactService` | `create()` | Contact d'urgence (permission `VIEW_PATIENT` + accès patient). |
| `MedicalConsentService` | `create()` | Consentement médical (vérifie organisation si fournie + accès patient). |

#### Security

| Service | Méthodes | Rôle |
|---|---|---|
| `PasswordManager` | `updatePassword()` | Vérifie l'ancien mot de passe, hache le nouveau, `flush()`. Lève `InvalidArgumentException` si l'ancien mot de passe est incorrect. |

#### Treatment

| Service | Méthodes | Rôle |
|---|---|---|
| `MedicationService` | `create()` | Médicament (`checkProfessionalAccess`, `MANAGE_MEDICATION`). |
| `PrescriptionService` | `create()` | Prescription (accès organisation + patient, `CREATE_PRESCRIPTION`). |
| `PrescriptionItemService` | `create()` | Ligne de prescription (`CREATE_PRESCRIPTION`). |
| `PrescriptionVersionService` | `create()` | Version de prescription (`UPDATE_PRESCRIPTION`). |
| `MedicationIntakeService` | `create()` | Prise de médicament (`RECORD_MEDICATION_INTAKE`). |

---

## 2. Couche DTO (`src/DTO/`)

### 2.1 Pattern général

Un couple **RequestDTO + ResponseDTO** par ressource, réparti en 11 modules (Appointment, Common, Communication, Healthcare, Identity, Medical, Notification, Nutrition, Patient, Treatment) : **39 RequestDTO** et **39 ResponseDTO**.

- **RequestDTO** (`src/DTO/Request/...`) : classe `readonly`, **contraintes de validation Symfony** (`#[Assert\NotBlank]`, `#[Assert\Regex]`, etc.) et **attributs OpenAPI** (`#[OA\Schema]`, `#[OA\Property]`) pour la documentation Swagger. Désérialisé par les contrôleurs via `#[MapRequestPayload]`.
- **ResponseDTO** (`src/DTO/Response/...`) : classe `readonly`, attributs OpenAPI, **factory statique** `fromEntity(Entity): self`.

### 2.2 Classes particulières

| Classe | Rôle |
|---|---|
| `App\DTO\Feedback` | **Wrapper universel** de réponse, retourné par tous les services. Implémente `JsonSerializable` : `{ flush, flushDescription, status, errors, warnings, data }`. API fluide : `setData()`, `addError()`, `addWarning()`, `bind($violations)` (fusion des violations du Validator), `autoInitFlush()` (statut 200/422 automatique), `hasErrors()`, `isOk()`, `hasWarnings()`. |
| `App\DTO\Response\HttpErrorResponsePayload` | Payload d'erreur HTTP normalisé par `HttpExceptionListener` : `status`, `error`, `message`, `details` (détail technique masqué hors dev/test). |
| `App\DTO\Response\Identity\UserResponseDTO` | Base des données utilisateur renvoyées : id, email, phone, fullName, avatarUrl, gender, locale, status, adresse, roles, dates. |
| `App\DTO\Response\Identity\PatientResponseDTO` | Aplatit l'adresse en champs `street`/`city`/`postalCode`/`country`. **Note** : aucun `PatientService`/`UserService` n'existe encore dans la couche service. |

---

## 3. Couche Mapper (`src/Mapper/`)

- **36 mappers**, un par ressource, miroir exact des services.
- Deux méthodes systématiques :
  - `mapRequestToEntity(RequestDTO $dto, Entity ...$parents, ?Entity $existing = null): Entity` — crée ou met à jour l'entité à partir du DTO ;
  - `mapEntityToResponse(Entity $entity): ResponseDTO` — délègue à `ResponseDTO::fromEntity()`.
- Les mappers liés (patient, professionnel, organisation…) sont injectés dans les mappers composites ou dans les services.

---

## 4. Gestion des erreurs et événements

| Classe | Type | Rôle |
|---|---|---|
| `ConstraintViolationExceptionListener` | EventListener (`kernel.exception`, priorité 10) | Transforme `ConstraintViolationListException` / `UnprocessableEntityHttpException` en réponse **422** avec le détail champ → message (via `Feedback->bind()`). |
| `HttpExceptionListener` | EventListener (`kernel.exception`, priorité 0) | Filet de sécurité : normalise toute exception HTTP en `JsonResponse` (`HttpErrorResponsePayload`). Mapping : `HttpExceptionInterface` → code + message ; `AccessDeniedException` → 403 ; `AuthenticationException` → 401 ; sinon 500. Ignore les `ConstraintViolationListException` (déjà traitées). Logs et détails techniques limités aux environnements dev/test. |
| `JWTCreatedSubscriber` | EventSubscriber (`lexik_jwt_authentication.on_jwt_created`) | Ajoute au JWT les claims `fullname` et `roles`. |

---

## 5. Commandes console

| Fichier | Commande | Rôle |
|---|---|---|
| `src/Command/CreateRootUserCommand.php` | `app:security:create-root` | Crée ou met à jour le compte `ROLE_ROOT`. Option `--email` (défaut `root@diabcare.com`), `--password` (obligatoire en prod, sinon généré aléatoirement). Journalisation `critical` de l'action. |
