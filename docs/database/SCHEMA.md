# Schéma de la base de données

Ce document décrit le modèle de données défini par les entités Doctrine (`src/Entity/`) : entités, tables, relations et enums.

## 0. Classes de base et conventions

| Classe | Type Doctrine | Rôle |
|---|---|---|
| `App\Entity\Common\BaseEntity` | `MappedSuperclass` | Classe mère de toutes les entités : `id` (BIGINT unsigned, auto), `createdAt` (datetime_immutable, non modifiable), `updatedAt` (datetime_immutable nullable). Lifecycle callbacks `prePersist`/`preUpdate`. |
| `App\Entity\Common\PatientCommonOperation` | `MappedSuperclass` | Super-classe des opérations/mesures/documents liés à un patient : `patient` (M2O → Patient, CASCADE), `issuer` (M2O → User, RESTRICT), `measuredAt` (datetime_immutable), `source` (enum `MeasurementSource`, nullable), `notes` (text). |
| `App\Entity\Identity\Person` | `MappedSuperclass` | Personne physique : `fullName`, `phone`, `gender` (enum), `avatarUrl`, adresse intégrée (`Address`, préfixe `address_`). |
| `App\Entity\Identity\User` | Entité abstraite, héritage **JOINED** | Utilisateur authentifiable (`UserInterface`, `PasswordAuthenticatedUserInterface`). Discriminateur `user_type` : `patient` / `professional` / `administrator`. Colonnes : `email` (unique), `passwordHash`, `locale` (déf. `fr`), `status` (enum), `emailVerifiedAt`, `lastLoginAt`. |

**Convention de nommage des tables** : `<domaine>_<nom_pluriel>` en snake_case.

---

## 1. Domaine Identity — `App\Entity\Identity`

| Table | Entité | Description |
|---|---|---|
| `identity_users` | `User` (JOINED) | Comptes utilisateurs (patient / professionnel / administrateur). |
| `identity_patients` | `Patient` | Patient : `dateOfBirth`, `placeOfBirth`, `bloodType`, `heightCm`. Rôle `ROLE_PATIENT`. |
| `identity_healthcare_professionals` | `HealthcareProfessional` | Professionnel : `licenseNumber` (unique), `professionalType`, `specialty`, `signatureUrl`. Rôles `ROLE_CLINICIAN` / `ROLE_NUTRITIONIST`. |
| `identity_administrators` | `Administrator` | Administrateur système. Rôle `ROLE_ROOT`. |

**Embeddable `Address`** (`identity_address` — non mappé en table, colonnes fusionnées avec `address_` préfixe) : `street`, `city`, `postalCode`, `country`, `state`.

### Enums Identity

| Enum | Valeurs |
|---|---|
| `Gender` | `MALE`, `FEMALE`, `OTHER`, `UNSPECIFIED` |
| `UserStatus` | `PENDING_ACTIVATION`, `ACTIVE`, `SUSPENDED`, `DISABLED` |
| `DiabetesType` | `TYPE_1`, `TYPE_2`, `GESTATIONAL`, `OTHER` |
| `ProfessionalType` | `CLINICIAN`, `NUTRITIONIST` |
| `Role` | `ROLE_ROOT`, `ROLE_ADMIN`, `ROLE_CLINICIAN`, `ROLE_NUTRITIONIST`, `ROLE_PATIENT` |

---

## 2. Domaine Healthcare — `App\Entity\Healthcare`

| Table | Entité | Description et colonnes principales |
|---|---|---|
| `healthcare_organizations` | `HealthcareOrganization` | `name`, `shortName`, `type` (enum), `email`, `phone`, `website`, `logoUrl`, adresse intégrée, `active` (bool). Relations : `facilities` (O2M), `memberships` (O2M). |
| `healthcare_facilities` | `HealthcareFacility` | Établissement : `name`, adresse intégrée, `phone`. Relations : `organization` (M2O), `departments` (O2M). |
| `healthcare_departments` | `Department` | `name`, `specialty`. Relation : `facility` (M2O). |
| `healthcare_organization_memberships` | `OrganizationMembership` | Adhésion user ↔ org : `startDate`, `endDate`, `status` (enum). Relations : `user` (M2O), `organization` (M2O), `facility` (M2O opt.), `department` (M2O opt.). |
| `healthcare_care_team_assignments` | `CareTeamAssignment` | Affectation d'un professionnel auprès d'un patient : `role` (enum), `startDate`, `endDate`, `active`. Relations : `patient`, `professional`, `organization` (M2O). |

### Enums Healthcare

| Enum | Valeurs |
|---|---|
| `OrganizationType` | `HOSPITAL`, `CLINIC`, `NETWORK` |
| `MembershipStatus` | `ACTIVE`, `SUSPENDED`, `ENDED` |
| `CareTeamRole` | `PRIMARY_CLINICIAN`, `SPECIALIST`, `NUTRITIONIST` |

---

## 3. Domaine Appointment — `App\Entity\Appointment`

| Table | Entité | Description |
|---|---|---|
| `appointment_appointments` | `Appointment` | `scheduledAt`, `durationMinutes`, `status` (enum), `reason`, `notes`. Relations : `patient`, `professional`, `organization` (M2O), `facility` (M2O opt.), `reminders` (O2M). |
| `appointment_appointment_reminders` | `AppointmentReminder` | `channel` (enum), `scheduledFor`, `sentAt`. Relation : `appointment` (M2O). |

### Enums Appointment

| Enum | Valeurs |
|---|---|
| `AppointmentStatus` | `SCHEDULED`, `CONFIRMED`, `COMPLETED`, `CANCELLED`, `NO_SHOW` |
| `ReminderChannel` | `EMAIL`, `SMS`, `PUSH`, `IN_APP` |

---

## 4. Domaine Medical — `App\Entity\Medical`

Toutes les entités héritent de `PatientCommonOperation` (patient, issuer, measuredAt, source, notes).

| Table | Entité | Description |
|---|---|---|
| `medical_medical_records` | `MedicalRecord` | `status` (enum), `openedAt`, `closedAt`. Relation : `organization` (M2O). |
| `medical_blood_glucose_measurements` | `BloodGlucoseMeasurement` | `value`, `unit` (enum `GlucoseUnit`), `context` (enum `GlucoseContext`). |
| `medical_blood_pressure_measurements` | `BloodPressureMeasurement` | `systolic`, `diastolic`, `pulse` (opt.). |
| `medical_hba1c_measurements` | `HbA1cMeasurement` | `valuePercent`. |
| `medical_weight_measurements` | `WeightMeasurement` | `valueKg`, `heightCm` (opt.), `bmi` (opt., recalculé). |
| `medical_physical_activity_measurements` | `PhysicalActivityMeasurement` | `activityType`, `durationMinutes`, `caloriesBurned`, `minHeartRate`, `maxHeartRate` (opt.). |
| `medical_laboratory_results` | `LaboratoryResult` | `testName`, `fileUrl` (opt.), `labName` (opt.). |
| `medical_diagnoses` | `Diagnosis` | `conditionName`, `description`, `diagnosedAt`, `status` (**chaîne simple**, non enum). Relations : `doctor` (M2O), `medicalRecord` (M2O opt.). |
| `medical_medical_notes` | `MedicalNote` | `content`, `notedAt`. Relations : `medicalRecord` (M2O), `author` (M2O → User). |

### Enums Medical

| Enum | Valeurs |
|---|---|
| `GlucoseUnit` | `MG_DL`, `MMOL_L` |
| `GlucoseContext` | `FASTING`, `BEFORE_MEAL`, `AFTER_MEAL`, `BEDTIME`, `RANDOM` |
| `MedicalRecordStatus` | `OPEN`, `CLOSED` |
| `MeasurementSource` (Common) | `MANUAL_ENTRY`, `CONNECTED_DEVICE`, `IMPORTED`, `CLINICIAN_ENTRY` |

---

## 5. Domaine Treatment — `App\Entity\Treatment`

| Table | Entité | Description |
|---|---|---|
| `treatment_medications` | `Medication` | `name`, `category` (enum), `description`, `insulinLevel`, `manufacturer`. |
| `treatment_prescriptions` | `Prescription` | `startDate`, `endDate`, `status` (enum), `notes`, `validatedAt`. Relations : `patient` (M2O), `prescriber` (M2O, RESTRICT), `organization` (M2O, RESTRICT), `validatedBy` (M2O opt.), `items` (O2M), `versions` (O2M). |
| `treatment_prescription_items` | `PrescriptionItem` | `dosage`, `quantity`, `morning`/`noon`/`evening` (bools), `instructions`. Relations : `prescription` (M2O), `medication` (M2O, RESTRICT), `intakes` (O2M). |
| `treatment_prescription_versions` | `PrescriptionVersion` | `versionNumber`, `changesSummary`, `data` (json). Relations : `prescription` (M2O), `modifiedBy` (M2O, RESTRICT). |
| `treatment_medication_intakes` | `MedicationIntake` | `takenAt`, `quantityTaken`, `status` (enum). Relations : `prescriptionItem` (M2O, CASCADE). |

### Enums Treatment

| Enum | Valeurs |
|---|---|
| `MedicationCategory` | `INSULIN`, `TABLET`, `OTHER` |
| `PrescriptionStatus` | `DRAFT`, `ACTIVE`, `COMPLETED`, `CANCELLED` |
| `IntakeStatus` | `TAKEN`, `SKIPPED`, `DELAYED` |

---

## 6. Domaine Nutrition — `App\Entity\Nutrition`

| Table | Entité | Description |
|---|---|---|
| `nutrition_food_categories` | `FoodCategory` | `label`, `description`. Relation : `foods` (O2M). |
| `nutrition_foods` | `Food` | `name`, `description`, `photoUrl`, `caloriesPer100g`, `carbsPer100g`, `proteinPer100g`, `fatPer100g`. Relations : `category` (M2O, RESTRICT), `createdBy` (M2O → HealthcareProfessional, opt.). |
| `nutrition_meals` | `Meal` | Hérite de `PatientCommonOperation`. `name`, `description`, `mealType` (enum). Relation : `mealItems` (O2M). |
| `nutrition_meal_items` | `MealItem` | `portionGrams`, `breadUnits` (opt., unités pain). Relations : `meal` (M2O, CASCADE), `food` (M2O, RESTRICT). |

### Enums Nutrition

| Enum | Valeurs |
|---|---|
| `MealType` | `BREAKFAST`, `LUNCH`, `DINNER`, `SNACK` |

---

## 7. Domaine Communication — `App\Entity\Communication`

| Table | Entité | Description |
|---|---|---|
| `communication_conversations` | `Conversation` | `subject`, `closedAt`. Relations : `patient` (M2O → **User**), `organization` (M2O opt.), `messages` (O2M). |
| `communication_messages` | `Message` | `content`, `sentAt`, `editedAt`. Relations : `conversation` (M2O), `sender` (M2O → User), `attachments` (O2M). |
| `communication_message_attachments` | `MessageAttachment` | `fileUrl`, `fileName`, `mimeType`, `sizeBytes`. Relation : `message` (M2O). |
| `communication_message_read_receipts` | `MessageReadReceipt` | `readAt`. Relations : `message` (M2O), `user` (M2O). |

---

## 8. Domaine Notification — `App\Entity\Notification`

| Table | Entité | Description |
|---|---|---|
| `notification_notifications` | `Notification` | `type` (enum), `title`, `body`, `channel` (enum `ReminderChannel`), `readAt`, `relatedEntityType`, `relatedEntityId`. Relation : `user` (M2O). |
| `notification_reminder_rules` | `ReminderRule` | `targetType` (enum), `relatedEntityId`, `cronExpression`, `active`. Relation : `patient` (M2O). |

### Enums Notification

| Enum | Valeurs |
|---|---|
| `NotificationType` | `MEDICATION_REMINDER`, `APPOINTMENT_REMINDER`, `MEASUREMENT_REMINDER`, `MESSAGE_RECEIVED`, `PRESCRIPTION_UPDATED`, `SYSTEM_ALERT` |
| `ReminderTargetType` | `MEDICATION`, `APPOINTMENT`, `MEASUREMENT` |

---

## 9. Domaine Patient — `App\Entity\Patient`

| Table | Entité | Description |
|---|---|---|
| `patient_allergies` | `Allergy` | `name`, `severity` (enum), `reaction`, `notes`, `diagnosedAt`. Relation : `patient` (M2O). |
| `patient_emergency_contacts` | `EmergencyContact` | `fullName`, `relationship`, `phone`, `email`. Relation : `patient` (M2O). |
| `patient_medical_consents` | `MedicalConsent` | `consentType` (enum), `grantedAt`, `revokedAt`, `documentUrl`. Relations : `patient` (M2O), `organization` (M2O opt.). |

### Enums Patient

| Enum | Valeurs |
|---|---|
| `AllergySeverity` | `MILD`, `MODERATE`, `SEVERE` |
| `ConsentType` | `DATA_PROCESSING`, `TELEMONITORING`, `DATA_SHARING_WITH_ORG` |

---

## 10. Domaine Common — `App\Entity\Common`

| Table | Entité | Description |
|---|---|---|
| `common_file_attachments` | `FileAttachment` | Hérite de `PatientCommonOperation`. `filename`, `mimeType`, `sizeBytes`, `caption`. |

---

## 11. Graphe relationnel principal

```text
User (JOINED)
 ├── Patient ───┬──> PatientCommonOperation (M2O)
 │              │      └── FileAttachment, BloodGlucose, BloodPressure, HbA1c,
 │              │          Weight, PhysicalActivity, LaboratoryResult, Meal, MedicationIntake
 │              ├──> MedicalRecord / Diagnosis / MedicalNote / Allergy /
 │              │    EmergencyContact / MedicalConsent / Prescription / ReminderRule
 │              └──> CareTeamAssignment (via professional + patient)
 ├── HealthcareProfessional ──> CareTeamAssignment, Diagnosis.doctor, Prescription.prescriber, Food.createdBy
 └── Administrator

HealthcareOrganization
 ├── HealthcareFacility ──> Department
 ├── OrganizationMembership (user + org + facility + department)
 ├── CareTeamAssignment / Appointment / MedicalRecord / MedicalConsent / Prescription
 └── Conversation (opt.)

Conversation ──> Message ──> MessageAttachment, MessageReadReceipt
Appointment ──> AppointmentReminder
Prescription ──> PrescriptionItem ──> MedicationIntake
Prescription ──> PrescriptionVersion
FoodCategory ──> Food; Meal ──> MealItem ──> Food
```

---

## 12. Récapitulatif chiffré

- **65 fichiers** dans `src/Entity/`
- **39 entités** (`#[ORM\Entity]`), **3 MappedSuperclass** (`BaseEntity`, `PatientCommonOperation`, `Person`), **1 Embeddable** (`Address`)
- **22 enums** PHP 8
- Tables issues du schéma initial : **~40 tables**
