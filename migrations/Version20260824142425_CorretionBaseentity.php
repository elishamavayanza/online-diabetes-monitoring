<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260824142425_CorretionBaseentity extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment_appointment_reminders ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment_appointments ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE common_file_attachments ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE communication_conversations ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE communication_message_attachments ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE communication_message_read_receipts ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE communication_messages ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE healthcare_care_team_assignments ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE healthcare_departments ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE healthcare_facilities ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE healthcare_organization_memberships ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE healthcare_organizations ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE identity_users ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE medical_blood_glucose_measurements ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE medical_blood_pressure_measurements ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE medical_diagnoses ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE medical_hba1c_measurements ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE medical_laboratory_results ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE medical_medical_notes ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE medical_medical_records ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE medical_physical_activity_measurements ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE medical_weight_measurements ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE notification_notifications ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE notification_reminder_rules ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE nutrition_food_categories ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE nutrition_foods ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE nutrition_meal_items ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE nutrition_meals ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_allergies ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_emergency_contacts ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_medical_consents ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE treatment_medication_intakes ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE treatment_medications ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE treatment_prescription_items ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE treatment_prescription_versions ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE treatment_prescriptions ADD deleted_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment_appointments DROP deleted_at');
        $this->addSql('ALTER TABLE appointment_appointment_reminders DROP deleted_at');
        $this->addSql('ALTER TABLE common_file_attachments DROP deleted_at');
        $this->addSql('ALTER TABLE communication_conversations DROP deleted_at');
        $this->addSql('ALTER TABLE communication_messages DROP deleted_at');
        $this->addSql('ALTER TABLE communication_message_attachments DROP deleted_at');
        $this->addSql('ALTER TABLE communication_message_read_receipts DROP deleted_at');
        $this->addSql('ALTER TABLE healthcare_care_team_assignments DROP deleted_at');
        $this->addSql('ALTER TABLE healthcare_departments DROP deleted_at');
        $this->addSql('ALTER TABLE healthcare_facilities DROP deleted_at');
        $this->addSql('ALTER TABLE healthcare_organizations DROP deleted_at');
        $this->addSql('ALTER TABLE healthcare_organization_memberships DROP deleted_at');
        $this->addSql('ALTER TABLE identity_users DROP deleted_at');
        $this->addSql('ALTER TABLE medical_blood_glucose_measurements DROP deleted_at');
        $this->addSql('ALTER TABLE medical_blood_pressure_measurements DROP deleted_at');
        $this->addSql('ALTER TABLE medical_diagnoses DROP deleted_at');
        $this->addSql('ALTER TABLE medical_hba1c_measurements DROP deleted_at');
        $this->addSql('ALTER TABLE medical_laboratory_results DROP deleted_at');
        $this->addSql('ALTER TABLE medical_medical_notes DROP deleted_at');
        $this->addSql('ALTER TABLE medical_medical_records DROP deleted_at');
        $this->addSql('ALTER TABLE medical_physical_activity_measurements DROP deleted_at');
        $this->addSql('ALTER TABLE medical_weight_measurements DROP deleted_at');
        $this->addSql('ALTER TABLE notification_notifications DROP deleted_at');
        $this->addSql('ALTER TABLE notification_reminder_rules DROP deleted_at');
        $this->addSql('ALTER TABLE nutrition_foods DROP deleted_at');
        $this->addSql('ALTER TABLE nutrition_food_categories DROP deleted_at');
        $this->addSql('ALTER TABLE nutrition_meals DROP deleted_at');
        $this->addSql('ALTER TABLE nutrition_meal_items DROP deleted_at');
        $this->addSql('ALTER TABLE patient_allergies DROP deleted_at');
        $this->addSql('ALTER TABLE patient_emergency_contacts DROP deleted_at');
        $this->addSql('ALTER TABLE patient_medical_consents DROP deleted_at');
        $this->addSql('ALTER TABLE treatment_medications DROP deleted_at');
        $this->addSql('ALTER TABLE treatment_medication_intakes DROP deleted_at');
        $this->addSql('ALTER TABLE treatment_prescriptions DROP deleted_at');
        $this->addSql('ALTER TABLE treatment_prescription_items DROP deleted_at');
        $this->addSql('ALTER TABLE treatment_prescription_versions DROP deleted_at');
    }
}
