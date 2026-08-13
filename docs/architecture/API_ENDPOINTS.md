# Référence des endpoints API

## 0. Vue d'ensemble

- **Préfixe général** : `/api`
- **Authentification** : JWT (Bearer token). Toutes les routes `/api/*` exigent un utilisateur authentifié (`IS_AUTHENTICATED_FULLY`) **sauf** les exceptions listées ci-dessous.
- **Accès publics** : `POST /api/login_check`, la documentation `GET /api/doc` / `GET /api/doc.json`, et la racine `GET /` (hors périmètre `/api`).
- **Format des réponses** : JSON. La majorité des réponses d'action passent par le wrapper `Feedback` : `{ status, error, message, data }` (et `flush`, `flushDescription`, `errors`, `warnings` côté service).
- **Body des requêtes** : JSON désérialisé dans un DTO Request via `#[MapRequestPayload]` (validation automatique → 422 si violation).
- **Paramètres d'identifiant** : UUID (`{id}`, `{patientId}`).

### Statuts HTTP employés

| Code | Signification |
|---|---|
| 200 | Succès (lecture, mise à jour, suppression) |
| 201 | Ressource créée |
| 400 | Données invalides / erreur métier / entité introuvable |
| 401 | Non authentifié |
| 403 | Accès refusé (permission / appartenance organisation) |
| 404 | Ressource introuvable |
| 422 | Violations de contraintes de validation |

---

## 1. Authentification

### `AuthController` — tag OpenAPI : `Authentication`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/login_check` | `api_login_check` | Connexion et obtention du token JWT. Vérifie email/mot de passe, renvoie `{ token, fullName, roles }`. | `username` (string, accepte aussi l'email), `password` (string), `remember_me` (bool, optionnel) | **Public** |
| POST | `/api/forgot-password` | `api_forgot_password` | Demande de réinitialisation de mot de passe. **Stub** : renvoie « Instructions envoyées par email » sans logique réelle. | `email` (string) | Protégé (firewall) |
| POST | `/api/change-password` | `api_change_password` | Modifie le mot de passe de l'utilisateur connecté. | `oldPassword`, `newPassword` | `IS_AUTHENTICATED_FULLY` |

> **Note** : il n'existe pas de routes `register`, `refresh`, `logout` ni de réinitialisation de mot de passe par token dans ce dépôt. Le champ `remember_me` est accepté mais non exploité.

---

## 2. Rendez-vous (Appointment)

### `AppointmentController` — préfixe `/api/appointments`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/appointments` | `api_appointments_create` | Créer un rendez-vous médical patient ↔ professionnel, avec vérification des permissions et appartenance à l'organisation. | `AppointmentRequestDTO` | Authentifié |

### `AppointmentReminderController` — préfixe `/api/appointment-reminders`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/appointment-reminders` | `api_appointment_reminders_create` | Programmer un rappel de rendez-vous sur un canal (SMS, Email, Push, In-App). | `AppointmentReminderRequestDTO` | Authentifié |

---

## 3. Communication

### `ConversationController` — préfixe `/api/conversations`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/conversations` | `api_conversations_create` | Créer une conversation (fil de discussion), rattachée optionnellement à une organisation. | `ConversationRequestDTO` | Authentifié |

### `MessageController` — préfixe `/api/messages`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/messages` | `api_messages_create` | Envoyer un message textuel dans une conversation existante. | `MessageRequestDTO` | Authentifié |

### `MessageAttachmentController` — préfixe `/api/message-attachments`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/message-attachments` | `api_message_attachments_create` | Associer une pièce jointe (métadonnées) à un message existant. | `MessageAttachmentRequestDTO` | Authentifié |

### `MessageReadReceiptController` — préfixe `/api/message-read-receipts`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/message-read-receipts` | `api_message_read_receipts_create` | Marquer un message comme lu (accusé de lecture). | `MessageReadReceiptRequestDTO` | Authentifié |

---

## 4. Organisations de santé (Healthcare)

### `HealthcareOrganizationController` — préfixe `/api/healthcare-organizations` (CRUD complet)

| Méthode | Chemin | Nom de route | Description | Paramètres | Accès |
|---|---|---|---|---|---|
| GET | `/api/healthcare-organizations` | `api_healthcare_organizations_list` | Lister les organisations avec pagination. | Query : `page` (int, déf. 1), `limit` (int, déf. 10, max 100) | Authentifié |
| GET | `/api/healthcare-organizations/{id}` | `api_healthcare_organizations_show` | Afficher une organisation. | Path : `id` (UUID) | Authentifié |
| POST | `/api/healthcare-organizations` | `api_healthcare_organizations_create` | Créer une organisation. | Body : `HealthcareOrganizationRequestDTO` | Authentifié |
| PUT/PATCH | `/api/healthcare-organizations/{id}` | `api_healthcare_organizations_update` | Modifier une organisation. | Path : `id` ; Body : DTO | Authentifié |
| DELETE | `/api/healthcare-organizations/{id}` | `api_healthcare_organizations_delete` | Supprimer définitivement une organisation. | Path : `id` | Authentifié |
| PATCH | `/api/healthcare-organizations/{id}/suspend` | `api_healthcare_organizations_suspend` | Suspendre/désactiver une organisation. | Path : `id` | Authentifié |

### `HealthcareFacilityController` — préfixe `/api/healthcare-facilities`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/healthcare-facilities` | `api_healthcare_facilities_create` | Créer un établissement de santé rattaché à une organisation. | `HealthcareFacilityRequestDTO` | Authentifié |

### `DepartmentController` — préfixe `/api/departments`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/departments` | `api_departments_create` | Créer un département/service au sein d'un établissement. | `DepartmentRequestDTO` | Authentifié |

### `OrganizationMembershipController` — préfixe `/api/organization-memberships`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/organization-memberships` | `api_organization_memberships_create` | Rattacher un utilisateur à une organisation (et optionnellement un établissement/un département). | `OrganizationMembershipRequestDTO` | Authentifié |

### `CareTeamAssignmentController` — préfixe `/api/care-team-assignments`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/care-team-assignments` | `api_care_team_assignments_create` | Affecter un professionnel (avec rôle) à un patient dans une organisation. | `CareTeamAssignmentRequestDTO` | Authentifié |

---

## 5. Identité

### `HealthcareProfessionalController` — préfixe `/api/professionals` (CRUD)

| Méthode | Chemin | Nom de route | Description | Paramètres | Accès |
|---|---|---|---|---|---|
| GET | `/api/professionals` | `api_professionals_list` | Lister les professionnels de santé actifs. | — | Authentifié |
| POST | `/api/professionals` | `api_professionals_create` | Inscrire un nouveau professionnel de santé. | Body : `HealthcareProfessionalRequestDTO` | Authentifié |
| GET | `/api/professionals/{id}` | `api_professionals_get_by_id` | Récupérer un professionnel par ID. | Path : `id` (UUID) | Authentifié |
| PUT/PATCH | `/api/professionals/{id}` | `api_professionals_update` | Modifier un professionnel (PUT = complet, PATCH = partiel). | Path : `id` ; Body : DTO | Authentifié |
| DELETE | `/api/professionals/{id}` | `api_professionals_delete` | Supprimer définitivement un professionnel. | Path : `id` | Authentifié |

---

## 6. Suivi médical (Medical)

> Toutes les mesures de ce domaine utilisent le chemin `POST /api/patients/{patientId}/...` et prennent le `patientId` (UUID) en paramètre de chemin.

### `BloodGlucoseMeasurementController` — préfixe `/api/patients/{patientId}/blood-glucose-measurements`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/patients/{patientId}/blood-glucose-measurements` | `api_blood_glucose_create` | Enregistrer une mesure de glycémie. | `BloodGlucoseMeasurementRequestDTO` | Authentifié |

### `BloodPressureMeasurementController` — préfixe `/api/patients/{patientId}/blood-pressure-measurements`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/patients/{patientId}/blood-pressure-measurements` | `api_blood_pressure_create` | Enregistrer une mesure de tension artérielle (systolique, diastolique, pouls). | `BloodPressureMeasurementRequestDTO` | Authentifié |

### `HbA1cMeasurementController` — préfixe `/api/patients/{patientId}/hba1c-measurements`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/patients/{patientId}/hba1c-measurements` | `api_hba1c_create` | Enregistrer une mesure d'HbA1c (hémoglobine glyquée). | `HbA1cMeasurementRequestDTO` | Authentifié |

### `WeightMeasurementController` — préfixe `/api/patients/{patientId}/weight-measurements`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/patients/{patientId}/weight-measurements` | `api_weight_measurements_create` | Enregistrer une mesure de poids (taille optionnelle pour le calcul de l'IMC). | `WeightMeasurementRequestDTO` | Authentifié |

### `PhysicalActivityMeasurementController` — préfixe `/api/patients/{patientId}/physical-activity-measurements`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/patients/{patientId}/physical-activity-measurements` | `api_physical_activity_create` | Enregistrer une activité physique (durée, calories, fréquences cardiaques). | `PhysicalActivityMeasurementRequestDTO` | Authentifié |

### `LaboratoryResultController` — préfixe `/api/patients/{patientId}/laboratory-results`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/patients/{patientId}/laboratory-results` | `api_laboratory_results_create` | Ajouter un résultat de laboratoire. | `LaboratoryResultRequestDTO` | Authentifié |

### `DiagnosisController` — préfixe `/api/diagnoses`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/diagnoses` | `api_diagnoses_create` | Créer un diagnostic médical (le patient est dans le DTO). | `DiagnosisRequestDTO` | Authentifié |

### `MedicalNoteController` — préfixe `/api/medical-notes`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/medical-notes` | `api_medical_notes_create` | Créer une note médicale sur un dossier médical. | `MedicalNoteRequestDTO` | Authentifié |

### `MedicalRecordController` — préfixe `/api/medical-records`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/medical-records` | `api_medical_records_create` | Ouvrir un dossier médical pour un patient dans une organisation. | `MedicalRecordRequestDTO` | Authentifié |

---

## 7. Notifications

### `NotificationController` — préfixe `/api/notifications`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/notifications` | `api_notifications_create` | Créer/envoyer une notification à un utilisateur. | `NotificationRequestDTO` | Authentifié |

### `ReminderRuleController` — préfixe `/api/reminder-rules`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/reminder-rules` | `api_reminder_rules_create` | Créer une règle de rappel automatisé (expression CRON) pour un patient. | `ReminderRuleRequestDTO` | Authentifié |

---

## 8. Nutrition

### `FoodCategoryController` — préfixe `/api/food-categories`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/food-categories` | `api_food_categories_create` | Créer une catégorie d'aliments. | `FoodCategoryRequestDTO` | Authentifié |

### `FoodController` — préfixe `/api/foods`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/foods` | `api_foods_create` | Créer un aliment avec valeurs nutritionnelles pour 100 g. | `FoodRequestDTO` | Authentifié |

### `MealController` — préfixe `/api/meals`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/meals` | `api_meals_create` | Créer un repas. | `MealRequestDTO` | Authentifié |

### `MealItemController` — préfixe `/api/meal-items`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/meal-items` | `api_meal_items_create` | Ajouter un aliment (portion en grammes) à un repas. | `MealItemRequestDTO` | Authentifié |

---

## 9. Patient

### `AllergyController` — préfixe `/api/allergies`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/allergies` | `api_allergies_create` | Enregistrer une allergie pour un patient. | `AllergyRequestDTO` | Authentifié |

### `EmergencyContactController` — préfixe `/api/emergency-contacts`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/emergency-contacts` | `api_emergency_contacts_create` | Ajouter un contact d'urgence à un patient. | `EmergencyContactRequestDTO` | Authentifié |

### `MedicalConsentController` — préfixe `/api/medical-consents`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/medical-consents` | `api_medical_consents_create` | Enregistrer un consentement médical pour un patient. | `MedicalConsentRequestDTO` | Authentifié |

---

## 10. Traitements (Treatment)

### `MedicationController` — préfixe `/api/medications`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/medications` | `api_medications_create` | Créer un médicament dans le catalogue. | `MedicationRequestDTO` | Authentifié |

### `PrescriptionController` — préfixe `/api/prescriptions`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/prescriptions` | `api_prescriptions_create` | Émettre une prescription médicale pour un patient. | `PrescriptionRequestDTO` | Authentifié |

### `PrescriptionItemController` — préfixe `/api/prescription-items`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/prescription-items` | `api_prescription_items_create` | Associer un médicament et sa posologie à une prescription. | `PrescriptionItemRequestDTO` | Authentifié |

### `PrescriptionVersionController` — préfixe `/api/prescription-versions`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/prescription-versions` | `api_prescription_versions_create` | Archiver une version/historique d'une prescription. | `PrescriptionVersionRequestDTO` | Authentifié |

### `MedicationIntakeController` — préfixe `/api/medication-intakes`

| Méthode | Chemin | Nom de route | Description | Corps | Accès |
|---|---|---|---|---|---|
| POST | `/api/medication-intakes` | `api_medication_intakes_create` | Enregistrer une prise de médicament liée à un élément de prescription. | `MedicationIntakeRequestDTO` | Authentifié |

---

## 11. Commun (Common)

### `FileAttachmentController` — préfixe `/api/file-attachments`

| Méthode | Chemin | Nom de route | Description | Paramètres | Accès |
|---|---|---|---|---|---|
| POST | `/api/file-attachments` | `api_file_attachments_create` | Enregistrer les métadonnées d'une pièce jointe liée à une entité (patient, consultation, ordonnance…). | Body : `FileAttachmentRequestDTO` | Authentifié |
| GET | `/api/file-attachments/{id}` | `api_file_attachments_show` | Récupérer les détails d'une pièce jointe (métadonnées). | Path : `id` (UUID) | Authentifié |

---

## 12. Permissions

### `PermissionController`

| Méthode | Chemin | Nom de route | Description | Paramètres | Accès |
|---|---|---|---|---|---|
| GET | `/api/permissions` | `api_permissions_list` | Liste de toutes les permissions disponibles (`[{name, value}]` construit depuis `SecurityAction`). | — | `#[IsGranted('IS_AUTHENTICATED_FULLY')]` |

---

## 13. Général

### `HomeController`

| Méthode | Chemin | Nom de route | Description | Accès |
|---|---|---|---|---|
| GET | `/` | `app_home` | Accueil de l'API : `{ message, version: "v1", documentation: "/api/doc" }`. | **Public** |

### Documentation

| Méthode | Chemin | Description | Accès |
|---|---|---|---|
| GET | `/api/doc` | Interface Swagger UI. | **Public** |
| GET | `/api/doc.json` | Documentation OpenAPI au format JSON. | **Public** |

---

## Récapitulatif chiffré

- **38 contrôleurs** (36 API + HomeController + PermissionController)
- **40 routes d'action** hors documentation
- Répartition des verbes : **POST** majoritaire (création), quelques **GET**, **PUT/PATCH/DELETE** uniquement sur les ressources à CRUD complet (organisations, professionnels) + `suspend`.
- Seuls deux contrôleurs portent un `#[IsGranted]` explicite : `AuthController` (`api_change_password`) et `PermissionController` (`api_permissions_list`). Toutes les autres routes sont protégées par la règle globale du firewall.
