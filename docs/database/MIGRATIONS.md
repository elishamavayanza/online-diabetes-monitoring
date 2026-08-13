# Migrations Doctrine

Ce document résume le contenu des fichiers de migration présents dans `migrations/`.

## Liste des migrations

| Fichier | Description |
|---|---|
| `Version20260811142233_InitialSchemaV1.php` | Création du schéma initial complet (~40 tables). |
| `Version20260811142241_InitialSchemaV1.php` | **Migration vide** (aucun SQL) — doublon horodaté de la précédente. |
| `Version20260811145344_V2.php` | Recréation des tables du domaine Communication (schéma divergent de V1). |
| `Version20260812103859_V3.php` | Ajout de la colonne `address_state` sur les tables avec adresse. |

---

## 1. `Version20260811142233_InitialSchemaV1.php` — Schéma initial

Tables créées par module :

- **Identité** : `identity_users`, `identity_administrators`, `identity_healthcare_professionals`, `identity_patients` (héritage JOINED : FK `id` → `identity_users`).
- **Organisations** : `healthcare_organizations`, `healthcare_facilities`, `healthcare_departments`, `healthcare_organization_memberships`, `healthcare_care_team_assignments`.
- **Médical** : `medical_medical_records`, `medical_diagnoses`, `medical_medical_notes`, `medical_blood_glucose_measurements`, `medical_blood_pressure_measurements`, `medical_hba1c_measurements`, `medical_weight_measurements`, `medical_physical_activity_measurements`, `medical_laboratory_results`.
- **Patient** : `patient_allergies`, `patient_emergency_contacts`, `patient_medical_consents`.
- **Traitement** : `treatment_prescriptions`, `treatment_prescription_items`, `treatment_prescription_versions`, `treatment_medications`, `treatment_medication_intakes`.
- **Nutrition** : `nutrition_food_categories`, `nutrition_foods`, `nutrition_meals`, `nutrition_meal_items`.
- **Communication** : `communication_conversations`, `communication_conversation_participants`, `communication_messages`, `communication_message_attachments`, `communication_message_read_receipts`.
- **Rendez-vous** : `appointment_appointments`, `appointment_appointment_reminders`.
- **Notifications** : `notification_notifications`, `notification_reminder_rules`.
- **Commun** : `common_file_attachments`.

Toutes les contraintes de clés étrangères (CASCADE / RESTRICT / SET NULL) sont configurées conformément aux relations décrites dans [SCHEMA.md](./SCHEMA.md).

## 2. `Version20260811142241_InitialSchemaV1.php` — Migration vide

Aucun SQL dans `up()` ni `down()`. Il s'agit d'une migration « doublon » de la précédente.

## 3. `Version20260811145344_V2.php` — Communication (schéma divergent)

Recrée les tables `communication_conversations`, `communication_messages`, `communication_message_attachments`, `communication_message_read_receipts` avec une **structure différente de V1** :

- `communication_conversations` : colonne `patient_id` + FK vers `identity_users` (au lieu de la table `conversation_participants` de V1).
- `communication_message_read_receipts` : colonne `user_id` (au lieu de `participant_id`).
- Pas de table `conversation_participants`.

> **Attention** : ces tables existent déjà dans V1. L'exécution de V1 puis V2 provoque un conflit « table already exists ». Voir [../known-issues.md](../known-issues.md).

## 4. `Version20260812103859_V3.php` — Colonne `address_state`

Ajoute `address_state VARCHAR(100) DEFAULT NULL` sur les tables comportant une adresse intégrée : `healthcare_facilities`, `healthcare_organizations`, `identity_users`.

---

## Commandes utiles

```bash
php bin/console doctrine:migrations:migrate   # applique les migrations en attente
php bin/console doctrine:migrations:list      # liste les migrations et leur état
php bin/console doctrine:migrations:diff      # génère une migration à partir des entités
php bin/console doctrine:migrations:execute <version> --up   # exécute une migration précise
```
