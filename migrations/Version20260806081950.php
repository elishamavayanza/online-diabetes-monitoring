<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260806081950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE allergies (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(150) NOT NULL, severity VARCHAR(50) NOT NULL, reaction LONGTEXT DEFAULT NULL, notes LONGTEXT DEFAULT NULL, diagnosed_at DATETIME NOT NULL, patient_id INT NOT NULL, UNIQUE INDEX UNIQ_8D19BF1BD17F50A6 (uuid), INDEX IDX_8D19BF1B6B899279 (patient_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE appointment_reminders (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, channel VARCHAR(50) NOT NULL, scheduled_for DATETIME NOT NULL, sent_at DATETIME DEFAULT NULL, appointment_id INT NOT NULL, UNIQUE INDEX UNIQ_F818D7DDD17F50A6 (uuid), INDEX IDX_F818D7DDE5B533F9 (appointment_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE appointments (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, scheduled_at DATETIME NOT NULL, duration_minutes INT NOT NULL, status VARCHAR(50) NOT NULL, reason VARCHAR(255) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, patient_id INT NOT NULL, professional_id INT NOT NULL, organization_id INT NOT NULL, facility_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_6A41727AD17F50A6 (uuid), INDEX IDX_6A41727A6B899279 (patient_id), INDEX IDX_6A41727ADB77003 (professional_id), INDEX IDX_6A41727A32C8A3DE (organization_id), INDEX IDX_6A41727AA7014910 (facility_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE blood_glucose_measurements (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, value NUMERIC(5, 2) NOT NULL, unit VARCHAR(50) NOT NULL, context VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_4EEDA861D17F50A6 (uuid), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE blood_pressure_measurements (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, systolic NUMERIC(5, 2) NOT NULL, diastolic NUMERIC(5, 2) NOT NULL, pulse NUMERIC(5, 2) DEFAULT NULL, UNIQUE INDEX UNIQ_CCACD6E9D17F50A6 (uuid), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE care_team_assignments (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, role VARCHAR(50) NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, active TINYINT NOT NULL, patient_id INT NOT NULL, professional_id INT NOT NULL, organization_id INT NOT NULL, UNIQUE INDEX UNIQ_A78FD1F3D17F50A6 (uuid), INDEX IDX_A78FD1F36B899279 (patient_id), INDEX IDX_A78FD1F3DB77003 (professional_id), INDEX IDX_A78FD1F332C8A3DE (organization_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE conversation_participants (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, joined_at DATETIME NOT NULL, left_at DATETIME DEFAULT NULL, conversation_id INT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_21821ED3D17F50A6 (uuid), INDEX IDX_21821ED39AC0396 (conversation_id), INDEX IDX_21821ED3A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE conversations (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, subject VARCHAR(255) NOT NULL, closed_at DATETIME DEFAULT NULL, organization_id INT DEFAULT NULL, created_by_id INT NOT NULL, UNIQUE INDEX UNIQ_C2521BF1D17F50A6 (uuid), INDEX IDX_C2521BF132C8A3DE (organization_id), INDEX IDX_C2521BF1B03A8386 (created_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE data_access_logs (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, resource_type VARCHAR(150) NOT NULL, resource_id CHAR(36) NOT NULL, reason LONGTEXT DEFAULT NULL, accessed_at DATETIME NOT NULL, accessed_by_id INT NOT NULL, patient_id INT NOT NULL, UNIQUE INDEX UNIQ_F996FF10D17F50A6 (uuid), INDEX IDX_F996FF10AFA04F13 (accessed_by_id), INDEX IDX_F996FF106B899279 (patient_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE departments (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(150) NOT NULL, specialty VARCHAR(150) DEFAULT NULL, facility_id INT NOT NULL, UNIQUE INDEX UNIQ_16AEB8D4D17F50A6 (uuid), INDEX IDX_16AEB8D4A7014910 (facility_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE diagnoses (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, condition_name VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, diagnosed_at DATETIME NOT NULL, status VARCHAR(50) NOT NULL, patient_id INT NOT NULL, doctor_id INT NOT NULL, medical_record_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_D2644031D17F50A6 (uuid), INDEX IDX_D26440316B899279 (patient_id), INDEX IDX_D264403187F4FB17 (doctor_id), INDEX IDX_D2644031B88E2BB6 (medical_record_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE emergency_contacts (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, full_name VARCHAR(150) NOT NULL, relationship VARCHAR(100) NOT NULL, phone VARCHAR(50) NOT NULL, email VARCHAR(180) DEFAULT NULL, patient_id INT NOT NULL, UNIQUE INDEX UNIQ_EBFF402ED17F50A6 (uuid), INDEX IDX_EBFF402E6B899279 (patient_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE file_attachments (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, original_name VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, mime_type VARCHAR(100) NOT NULL, size_bytes INT NOT NULL, url VARCHAR(500) NOT NULL, entity_type VARCHAR(100) NOT NULL, entity_id BINARY(16) NOT NULL, uploaded_by_id INT NOT NULL, UNIQUE INDEX UNIQ_657F04B4D17F50A6 (uuid), INDEX IDX_657F04B4A2B28FE8 (uploaded_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE food_categories (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, label VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_5DB6F6C9D17F50A6 (uuid), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE foods (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, photo_url VARCHAR(500) DEFAULT NULL, calories_per100g NUMERIC(6, 2) NOT NULL, carbs_per100g NUMERIC(5, 2) NOT NULL, protein_per100g NUMERIC(5, 2) NOT NULL, fat_per100g NUMERIC(5, 2) NOT NULL, category_id INT NOT NULL, created_by_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_3803909DD17F50A6 (uuid), INDEX IDX_3803909D12469DE2 (category_id), INDEX IDX_3803909DB03A8386 (created_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE hba1c_measurements (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, value_percent NUMERIC(4, 2) NOT NULL, UNIQUE INDEX UNIQ_62C890FD17F50A6 (uuid), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE healthcare_facilities (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(150) NOT NULL, phone VARCHAR(50) DEFAULT NULL, address_street VARCHAR(255) DEFAULT NULL, address_city VARCHAR(100) DEFAULT NULL, address_postal_code VARCHAR(20) DEFAULT NULL, address_country VARCHAR(100) DEFAULT NULL, organization_id INT NOT NULL, UNIQUE INDEX UNIQ_940F83CFD17F50A6 (uuid), INDEX IDX_940F83CF32C8A3DE (organization_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE healthcare_organizations (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(150) NOT NULL, short_name VARCHAR(50) DEFAULT NULL, type VARCHAR(50) NOT NULL, email VARCHAR(180) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, logo_url VARCHAR(500) DEFAULT NULL, active TINYINT NOT NULL, address_street VARCHAR(255) DEFAULT NULL, address_city VARCHAR(100) DEFAULT NULL, address_postal_code VARCHAR(20) DEFAULT NULL, address_country VARCHAR(100) DEFAULT NULL, UNIQUE INDEX UNIQ_3BF9EABDD17F50A6 (uuid), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE healthcare_professionals (license_number VARCHAR(100) NOT NULL, professional_type VARCHAR(50) NOT NULL, specialty VARCHAR(150) DEFAULT NULL, signature_url VARCHAR(500) DEFAULT NULL, id INT NOT NULL, UNIQUE INDEX UNIQ_543BC64CEC7E7152 (license_number), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE laboratory_results (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, test_name VARCHAR(150) NOT NULL, file_url VARCHAR(500) DEFAULT NULL, lab_name VARCHAR(150) DEFAULT NULL, UNIQUE INDEX UNIQ_DC4C9AADD17F50A6 (uuid), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE meal_items (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, portion_grams NUMERIC(6, 2) NOT NULL, bread_units NUMERIC(5, 2) DEFAULT NULL, meal_id INT NOT NULL, food_id INT NOT NULL, UNIQUE INDEX UNIQ_5BD4019BD17F50A6 (uuid), INDEX IDX_5BD4019B639666D6 (meal_id), INDEX IDX_5BD4019BBA8E87C4 (food_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE meals (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, meal_type VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_E229E6EAD17F50A6 (uuid), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE medical_consents (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, consent_type VARCHAR(50) NOT NULL, granted_at DATETIME NOT NULL, revoked_at DATETIME DEFAULT NULL, document_url VARCHAR(500) DEFAULT NULL, patient_id INT NOT NULL, organization_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_7A402030D17F50A6 (uuid), INDEX IDX_7A4020306B899279 (patient_id), INDEX IDX_7A40203032C8A3DE (organization_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE medical_notes (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, content LONGTEXT NOT NULL, noted_at DATETIME NOT NULL, medical_record_id INT NOT NULL, author_id INT NOT NULL, UNIQUE INDEX UNIQ_34FDFDDCD17F50A6 (uuid), INDEX IDX_34FDFDDCB88E2BB6 (medical_record_id), INDEX IDX_34FDFDDCF675F31B (author_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE medical_records (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, status VARCHAR(50) NOT NULL, opened_at DATETIME NOT NULL, closed_at DATETIME DEFAULT NULL, patient_id INT NOT NULL, organization_id INT NOT NULL, UNIQUE INDEX UNIQ_DA9FB888D17F50A6 (uuid), INDEX IDX_DA9FB8886B899279 (patient_id), INDEX IDX_DA9FB88832C8A3DE (organization_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE medication_intakes (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, taken_at DATETIME NOT NULL, quantity_taken NUMERIC(6, 2) NOT NULL, status VARCHAR(50) NOT NULL, prescription_item_id INT NOT NULL, UNIQUE INDEX UNIQ_C579596D17F50A6 (uuid), INDEX IDX_C57959665C37E20 (prescription_item_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE medications (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, name VARCHAR(150) NOT NULL, category VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, insulin_level INT DEFAULT NULL, manufacturer VARCHAR(150) DEFAULT NULL, UNIQUE INDEX UNIQ_4B51506CD17F50A6 (uuid), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message_attachments (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, file_url VARCHAR(500) NOT NULL, file_name VARCHAR(255) NOT NULL, mime_type VARCHAR(150) NOT NULL, size_bytes INT NOT NULL, message_id INT NOT NULL, UNIQUE INDEX UNIQ_27BBA42FD17F50A6 (uuid), INDEX IDX_27BBA42F537A1329 (message_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message_read_receipts (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, read_at DATETIME NOT NULL, message_id INT NOT NULL, participant_id INT NOT NULL, UNIQUE INDEX UNIQ_B0F48DB3D17F50A6 (uuid), INDEX IDX_B0F48DB3537A1329 (message_id), INDEX IDX_B0F48DB39D1C3019 (participant_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, content LONGTEXT NOT NULL, sent_at DATETIME NOT NULL, edited_at DATETIME DEFAULT NULL, conversation_id INT NOT NULL, sender_id INT NOT NULL, UNIQUE INDEX UNIQ_DB021E96D17F50A6 (uuid), INDEX IDX_DB021E969AC0396 (conversation_id), INDEX IDX_DB021E96F624B39D (sender_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, type VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, channel VARCHAR(50) NOT NULL, read_at DATETIME DEFAULT NULL, related_entity_type VARCHAR(150) DEFAULT NULL, related_entity_id CHAR(36) DEFAULT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_6000B0D3D17F50A6 (uuid), INDEX IDX_6000B0D3A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE organization_memberships (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, status VARCHAR(50) NOT NULL, user_id INT NOT NULL, organization_id INT NOT NULL, facility_id INT DEFAULT NULL, department_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_B606E30DD17F50A6 (uuid), INDEX IDX_B606E30DA76ED395 (user_id), INDEX IDX_B606E30D32C8A3DE (organization_id), INDEX IDX_B606E30DA7014910 (facility_id), INDEX IDX_B606E30DAE80F5DF (department_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE patients (date_of_birth DATE DEFAULT NULL, place_of_birth VARCHAR(150) DEFAULT NULL, blood_type VARCHAR(10) DEFAULT NULL, height_cm NUMERIC(5, 2) DEFAULT NULL, id INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE physical_activity_measurements (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, activity_type VARCHAR(100) NOT NULL, duration_minutes INT NOT NULL, calories_burned NUMERIC(6, 2) DEFAULT NULL, min_heart_rate NUMERIC(5, 2) DEFAULT NULL, max_heart_rate NUMERIC(5, 2) DEFAULT NULL, UNIQUE INDEX UNIQ_276EE03FD17F50A6 (uuid), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE prescription_items (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, dosage VARCHAR(100) NOT NULL, quantity NUMERIC(6, 2) NOT NULL, morning TINYINT NOT NULL, noon TINYINT NOT NULL, evening TINYINT NOT NULL, instructions LONGTEXT DEFAULT NULL, prescription_id INT NOT NULL, medication_id INT NOT NULL, UNIQUE INDEX UNIQ_2CCBB162D17F50A6 (uuid), INDEX IDX_2CCBB16293DB413D (prescription_id), INDEX IDX_2CCBB1622C4DE6DA (medication_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE prescription_versions (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, version_number INT NOT NULL, changes_summary LONGTEXT DEFAULT NULL, data JSON NOT NULL, modified_at DATETIME NOT NULL, prescription_id INT NOT NULL, modified_by_id INT NOT NULL, UNIQUE INDEX UNIQ_AE16B0C5D17F50A6 (uuid), INDEX IDX_AE16B0C593DB413D (prescription_id), INDEX IDX_AE16B0C599049ECE (modified_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE prescriptions (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, status VARCHAR(50) NOT NULL, notes LONGTEXT DEFAULT NULL, validated_at DATETIME DEFAULT NULL, patient_id INT NOT NULL, prescriber_id INT NOT NULL, organization_id INT NOT NULL, validated_by_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_E41E1AC3D17F50A6 (uuid), INDEX IDX_E41E1AC36B899279 (patient_id), INDEX IDX_E41E1AC39861216F (prescriber_id), INDEX IDX_E41E1AC332C8A3DE (organization_id), INDEX IDX_E41E1AC3C69DE5E5 (validated_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reminder_rules (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, target_type VARCHAR(50) NOT NULL, related_entity_id CHAR(36) DEFAULT NULL, cron_expression VARCHAR(100) NOT NULL, active TINYINT NOT NULL, patient_id INT NOT NULL, UNIQUE INDEX UNIQ_CB3FFD8CD17F50A6 (uuid), INDEX IDX_CB3FFD8C6B899279 (patient_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, email VARCHAR(180) NOT NULL, password_hash VARCHAR(255) NOT NULL, phone VARCHAR(50) DEFAULT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, avatar_url VARCHAR(500) DEFAULT NULL, gender VARCHAR(50) NOT NULL, locale VARCHAR(10) NOT NULL, status VARCHAR(50) NOT NULL, email_verified_at DATETIME DEFAULT NULL, last_login_at DATETIME DEFAULT NULL, address_street VARCHAR(255) DEFAULT NULL, address_city VARCHAR(100) DEFAULT NULL, address_postal_code VARCHAR(20) DEFAULT NULL, address_country VARCHAR(100) DEFAULT NULL, user_type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9D17F50A6 (uuid), UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE weight_measurements (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, value_kg NUMERIC(5, 2) NOT NULL, height_cm NUMERIC(5, 2) DEFAULT NULL, bmi NUMERIC(5, 2) DEFAULT NULL, UNIQUE INDEX UNIQ_67EB72B4D17F50A6 (uuid), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE allergies ADD CONSTRAINT FK_8D19BF1B6B899279 FOREIGN KEY (patient_id) REFERENCES patients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointment_reminders ADD CONSTRAINT FK_F818D7DDE5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointments (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointments ADD CONSTRAINT FK_6A41727A6B899279 FOREIGN KEY (patient_id) REFERENCES patients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointments ADD CONSTRAINT FK_6A41727ADB77003 FOREIGN KEY (professional_id) REFERENCES healthcare_professionals (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointments ADD CONSTRAINT FK_6A41727A32C8A3DE FOREIGN KEY (organization_id) REFERENCES healthcare_organizations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointments ADD CONSTRAINT FK_6A41727AA7014910 FOREIGN KEY (facility_id) REFERENCES healthcare_facilities (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE care_team_assignments ADD CONSTRAINT FK_A78FD1F36B899279 FOREIGN KEY (patient_id) REFERENCES patients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE care_team_assignments ADD CONSTRAINT FK_A78FD1F3DB77003 FOREIGN KEY (professional_id) REFERENCES healthcare_professionals (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE care_team_assignments ADD CONSTRAINT FK_A78FD1F332C8A3DE FOREIGN KEY (organization_id) REFERENCES healthcare_organizations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversation_participants ADD CONSTRAINT FK_21821ED39AC0396 FOREIGN KEY (conversation_id) REFERENCES conversations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversation_participants ADD CONSTRAINT FK_21821ED3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF132C8A3DE FOREIGN KEY (organization_id) REFERENCES healthcare_organizations (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF1B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE data_access_logs ADD CONSTRAINT FK_F996FF10AFA04F13 FOREIGN KEY (accessed_by_id) REFERENCES users (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE data_access_logs ADD CONSTRAINT FK_F996FF106B899279 FOREIGN KEY (patient_id) REFERENCES patients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE departments ADD CONSTRAINT FK_16AEB8D4A7014910 FOREIGN KEY (facility_id) REFERENCES healthcare_facilities (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE diagnoses ADD CONSTRAINT FK_D26440316B899279 FOREIGN KEY (patient_id) REFERENCES patients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE diagnoses ADD CONSTRAINT FK_D264403187F4FB17 FOREIGN KEY (doctor_id) REFERENCES healthcare_professionals (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE diagnoses ADD CONSTRAINT FK_D2644031B88E2BB6 FOREIGN KEY (medical_record_id) REFERENCES medical_records (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE emergency_contacts ADD CONSTRAINT FK_EBFF402E6B899279 FOREIGN KEY (patient_id) REFERENCES patients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE file_attachments ADD CONSTRAINT FK_657F04B4A2B28FE8 FOREIGN KEY (uploaded_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE foods ADD CONSTRAINT FK_3803909D12469DE2 FOREIGN KEY (category_id) REFERENCES food_categories (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE foods ADD CONSTRAINT FK_3803909DB03A8386 FOREIGN KEY (created_by_id) REFERENCES healthcare_professionals (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE healthcare_facilities ADD CONSTRAINT FK_940F83CF32C8A3DE FOREIGN KEY (organization_id) REFERENCES healthcare_organizations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE healthcare_professionals ADD CONSTRAINT FK_543BC64CBF396750 FOREIGN KEY (id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meal_items ADD CONSTRAINT FK_5BD4019B639666D6 FOREIGN KEY (meal_id) REFERENCES meals (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meal_items ADD CONSTRAINT FK_5BD4019BBA8E87C4 FOREIGN KEY (food_id) REFERENCES foods (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE medical_consents ADD CONSTRAINT FK_7A4020306B899279 FOREIGN KEY (patient_id) REFERENCES patients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE medical_consents ADD CONSTRAINT FK_7A40203032C8A3DE FOREIGN KEY (organization_id) REFERENCES healthcare_organizations (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE medical_notes ADD CONSTRAINT FK_34FDFDDCB88E2BB6 FOREIGN KEY (medical_record_id) REFERENCES medical_records (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE medical_notes ADD CONSTRAINT FK_34FDFDDCF675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE medical_records ADD CONSTRAINT FK_DA9FB8886B899279 FOREIGN KEY (patient_id) REFERENCES patients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE medical_records ADD CONSTRAINT FK_DA9FB88832C8A3DE FOREIGN KEY (organization_id) REFERENCES healthcare_organizations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE medication_intakes ADD CONSTRAINT FK_C57959665C37E20 FOREIGN KEY (prescription_item_id) REFERENCES prescription_items (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_attachments ADD CONSTRAINT FK_27BBA42F537A1329 FOREIGN KEY (message_id) REFERENCES messages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_read_receipts ADD CONSTRAINT FK_B0F48DB3537A1329 FOREIGN KEY (message_id) REFERENCES messages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_read_receipts ADD CONSTRAINT FK_B0F48DB39D1C3019 FOREIGN KEY (participant_id) REFERENCES conversation_participants (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E969AC0396 FOREIGN KEY (conversation_id) REFERENCES conversations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96F624B39D FOREIGN KEY (sender_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organization_memberships ADD CONSTRAINT FK_B606E30DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organization_memberships ADD CONSTRAINT FK_B606E30D32C8A3DE FOREIGN KEY (organization_id) REFERENCES healthcare_organizations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organization_memberships ADD CONSTRAINT FK_B606E30DA7014910 FOREIGN KEY (facility_id) REFERENCES healthcare_facilities (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE organization_memberships ADD CONSTRAINT FK_B606E30DAE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE patients ADD CONSTRAINT FK_2CCC2E2CBF396750 FOREIGN KEY (id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE prescription_items ADD CONSTRAINT FK_2CCBB16293DB413D FOREIGN KEY (prescription_id) REFERENCES prescriptions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE prescription_items ADD CONSTRAINT FK_2CCBB1622C4DE6DA FOREIGN KEY (medication_id) REFERENCES medications (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE prescription_versions ADD CONSTRAINT FK_AE16B0C593DB413D FOREIGN KEY (prescription_id) REFERENCES prescriptions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE prescription_versions ADD CONSTRAINT FK_AE16B0C599049ECE FOREIGN KEY (modified_by_id) REFERENCES healthcare_professionals (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE prescriptions ADD CONSTRAINT FK_E41E1AC36B899279 FOREIGN KEY (patient_id) REFERENCES patients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE prescriptions ADD CONSTRAINT FK_E41E1AC39861216F FOREIGN KEY (prescriber_id) REFERENCES healthcare_professionals (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE prescriptions ADD CONSTRAINT FK_E41E1AC332C8A3DE FOREIGN KEY (organization_id) REFERENCES healthcare_organizations (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE prescriptions ADD CONSTRAINT FK_E41E1AC3C69DE5E5 FOREIGN KEY (validated_by_id) REFERENCES healthcare_professionals (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE reminder_rules ADD CONSTRAINT FK_CB3FFD8C6B899279 FOREIGN KEY (patient_id) REFERENCES patients (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE allergies DROP FOREIGN KEY FK_8D19BF1B6B899279');
        $this->addSql('ALTER TABLE appointment_reminders DROP FOREIGN KEY FK_F818D7DDE5B533F9');
        $this->addSql('ALTER TABLE appointments DROP FOREIGN KEY FK_6A41727A6B899279');
        $this->addSql('ALTER TABLE appointments DROP FOREIGN KEY FK_6A41727ADB77003');
        $this->addSql('ALTER TABLE appointments DROP FOREIGN KEY FK_6A41727A32C8A3DE');
        $this->addSql('ALTER TABLE appointments DROP FOREIGN KEY FK_6A41727AA7014910');
        $this->addSql('ALTER TABLE care_team_assignments DROP FOREIGN KEY FK_A78FD1F36B899279');
        $this->addSql('ALTER TABLE care_team_assignments DROP FOREIGN KEY FK_A78FD1F3DB77003');
        $this->addSql('ALTER TABLE care_team_assignments DROP FOREIGN KEY FK_A78FD1F332C8A3DE');
        $this->addSql('ALTER TABLE conversation_participants DROP FOREIGN KEY FK_21821ED39AC0396');
        $this->addSql('ALTER TABLE conversation_participants DROP FOREIGN KEY FK_21821ED3A76ED395');
        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF132C8A3DE');
        $this->addSql('ALTER TABLE conversations DROP FOREIGN KEY FK_C2521BF1B03A8386');
        $this->addSql('ALTER TABLE data_access_logs DROP FOREIGN KEY FK_F996FF10AFA04F13');
        $this->addSql('ALTER TABLE data_access_logs DROP FOREIGN KEY FK_F996FF106B899279');
        $this->addSql('ALTER TABLE departments DROP FOREIGN KEY FK_16AEB8D4A7014910');
        $this->addSql('ALTER TABLE diagnoses DROP FOREIGN KEY FK_D26440316B899279');
        $this->addSql('ALTER TABLE diagnoses DROP FOREIGN KEY FK_D264403187F4FB17');
        $this->addSql('ALTER TABLE diagnoses DROP FOREIGN KEY FK_D2644031B88E2BB6');
        $this->addSql('ALTER TABLE emergency_contacts DROP FOREIGN KEY FK_EBFF402E6B899279');
        $this->addSql('ALTER TABLE file_attachments DROP FOREIGN KEY FK_657F04B4A2B28FE8');
        $this->addSql('ALTER TABLE foods DROP FOREIGN KEY FK_3803909D12469DE2');
        $this->addSql('ALTER TABLE foods DROP FOREIGN KEY FK_3803909DB03A8386');
        $this->addSql('ALTER TABLE healthcare_facilities DROP FOREIGN KEY FK_940F83CF32C8A3DE');
        $this->addSql('ALTER TABLE healthcare_professionals DROP FOREIGN KEY FK_543BC64CBF396750');
        $this->addSql('ALTER TABLE meal_items DROP FOREIGN KEY FK_5BD4019B639666D6');
        $this->addSql('ALTER TABLE meal_items DROP FOREIGN KEY FK_5BD4019BBA8E87C4');
        $this->addSql('ALTER TABLE medical_consents DROP FOREIGN KEY FK_7A4020306B899279');
        $this->addSql('ALTER TABLE medical_consents DROP FOREIGN KEY FK_7A40203032C8A3DE');
        $this->addSql('ALTER TABLE medical_notes DROP FOREIGN KEY FK_34FDFDDCB88E2BB6');
        $this->addSql('ALTER TABLE medical_notes DROP FOREIGN KEY FK_34FDFDDCF675F31B');
        $this->addSql('ALTER TABLE medical_records DROP FOREIGN KEY FK_DA9FB8886B899279');
        $this->addSql('ALTER TABLE medical_records DROP FOREIGN KEY FK_DA9FB88832C8A3DE');
        $this->addSql('ALTER TABLE medication_intakes DROP FOREIGN KEY FK_C57959665C37E20');
        $this->addSql('ALTER TABLE message_attachments DROP FOREIGN KEY FK_27BBA42F537A1329');
        $this->addSql('ALTER TABLE message_read_receipts DROP FOREIGN KEY FK_B0F48DB3537A1329');
        $this->addSql('ALTER TABLE message_read_receipts DROP FOREIGN KEY FK_B0F48DB39D1C3019');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E969AC0396');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96F624B39D');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3A76ED395');
        $this->addSql('ALTER TABLE organization_memberships DROP FOREIGN KEY FK_B606E30DA76ED395');
        $this->addSql('ALTER TABLE organization_memberships DROP FOREIGN KEY FK_B606E30D32C8A3DE');
        $this->addSql('ALTER TABLE organization_memberships DROP FOREIGN KEY FK_B606E30DA7014910');
        $this->addSql('ALTER TABLE organization_memberships DROP FOREIGN KEY FK_B606E30DAE80F5DF');
        $this->addSql('ALTER TABLE patients DROP FOREIGN KEY FK_2CCC2E2CBF396750');
        $this->addSql('ALTER TABLE prescription_items DROP FOREIGN KEY FK_2CCBB16293DB413D');
        $this->addSql('ALTER TABLE prescription_items DROP FOREIGN KEY FK_2CCBB1622C4DE6DA');
        $this->addSql('ALTER TABLE prescription_versions DROP FOREIGN KEY FK_AE16B0C593DB413D');
        $this->addSql('ALTER TABLE prescription_versions DROP FOREIGN KEY FK_AE16B0C599049ECE');
        $this->addSql('ALTER TABLE prescriptions DROP FOREIGN KEY FK_E41E1AC36B899279');
        $this->addSql('ALTER TABLE prescriptions DROP FOREIGN KEY FK_E41E1AC39861216F');
        $this->addSql('ALTER TABLE prescriptions DROP FOREIGN KEY FK_E41E1AC332C8A3DE');
        $this->addSql('ALTER TABLE prescriptions DROP FOREIGN KEY FK_E41E1AC3C69DE5E5');
        $this->addSql('ALTER TABLE reminder_rules DROP FOREIGN KEY FK_CB3FFD8C6B899279');
        $this->addSql('DROP TABLE allergies');
        $this->addSql('DROP TABLE appointment_reminders');
        $this->addSql('DROP TABLE appointments');
        $this->addSql('DROP TABLE blood_glucose_measurements');
        $this->addSql('DROP TABLE blood_pressure_measurements');
        $this->addSql('DROP TABLE care_team_assignments');
        $this->addSql('DROP TABLE conversation_participants');
        $this->addSql('DROP TABLE conversations');
        $this->addSql('DROP TABLE data_access_logs');
        $this->addSql('DROP TABLE departments');
        $this->addSql('DROP TABLE diagnoses');
        $this->addSql('DROP TABLE emergency_contacts');
        $this->addSql('DROP TABLE file_attachments');
        $this->addSql('DROP TABLE food_categories');
        $this->addSql('DROP TABLE foods');
        $this->addSql('DROP TABLE hba1c_measurements');
        $this->addSql('DROP TABLE healthcare_facilities');
        $this->addSql('DROP TABLE healthcare_organizations');
        $this->addSql('DROP TABLE healthcare_professionals');
        $this->addSql('DROP TABLE laboratory_results');
        $this->addSql('DROP TABLE meal_items');
        $this->addSql('DROP TABLE meals');
        $this->addSql('DROP TABLE medical_consents');
        $this->addSql('DROP TABLE medical_notes');
        $this->addSql('DROP TABLE medical_records');
        $this->addSql('DROP TABLE medication_intakes');
        $this->addSql('DROP TABLE medications');
        $this->addSql('DROP TABLE message_attachments');
        $this->addSql('DROP TABLE message_read_receipts');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE organization_memberships');
        $this->addSql('DROP TABLE patients');
        $this->addSql('DROP TABLE physical_activity_measurements');
        $this->addSql('DROP TABLE prescription_items');
        $this->addSql('DROP TABLE prescription_versions');
        $this->addSql('DROP TABLE prescriptions');
        $this->addSql('DROP TABLE reminder_rules');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE weight_measurements');
    }
}
