<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260907142837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de l\'auteur (created_by_id) sur allergies, contacts d\'urgence, consentements, éléments de repas et éléments de prescription.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nutrition_meal_items ADD created_by_id BIGINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE nutrition_meal_items ADD CONSTRAINT FK_FC4CAD0CB03A8386 FOREIGN KEY (created_by_id) REFERENCES identity_users (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_FC4CAD0CB03A8386 ON nutrition_meal_items (created_by_id)');
        $this->addSql('ALTER TABLE patient_allergies ADD created_by_id BIGINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_allergies ADD CONSTRAINT FK_2B926D24B03A8386 FOREIGN KEY (created_by_id) REFERENCES identity_users (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_2B926D24B03A8386 ON patient_allergies (created_by_id)');
        $this->addSql('ALTER TABLE patient_emergency_contacts ADD created_by_id BIGINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_emergency_contacts ADD CONSTRAINT FK_1B1B72B7B03A8386 FOREIGN KEY (created_by_id) REFERENCES identity_users (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_1B1B72B7B03A8386 ON patient_emergency_contacts (created_by_id)');
        $this->addSql('ALTER TABLE patient_medical_consents ADD created_by_id BIGINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE patient_medical_consents ADD CONSTRAINT FK_D5AB8FF5B03A8386 FOREIGN KEY (created_by_id) REFERENCES identity_users (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_D5AB8FF5B03A8386 ON patient_medical_consents (created_by_id)');
        $this->addSql('ALTER TABLE treatment_prescription_items ADD created_by_id BIGINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE treatment_prescription_items ADD CONSTRAINT FK_4F5A21AEB03A8386 FOREIGN KEY (created_by_id) REFERENCES identity_users (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_4F5A21AEB03A8386 ON treatment_prescription_items (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nutrition_meal_items DROP FOREIGN KEY FK_FC4CAD0CB03A8386');
        $this->addSql('DROP INDEX IDX_FC4CAD0CB03A8386 ON nutrition_meal_items');
        $this->addSql('ALTER TABLE nutrition_meal_items DROP created_by_id');
        $this->addSql('ALTER TABLE patient_allergies DROP FOREIGN KEY FK_2B926D24B03A8386');
        $this->addSql('DROP INDEX IDX_2B926D24B03A8386 ON patient_allergies');
        $this->addSql('ALTER TABLE patient_allergies DROP created_by_id');
        $this->addSql('ALTER TABLE patient_emergency_contacts DROP FOREIGN KEY FK_1B1B72B7B03A8386');
        $this->addSql('DROP INDEX IDX_1B1B72B7B03A8386 ON patient_emergency_contacts');
        $this->addSql('ALTER TABLE patient_emergency_contacts DROP created_by_id');
        $this->addSql('ALTER TABLE patient_medical_consents DROP FOREIGN KEY FK_D5AB8FF5B03A8386');
        $this->addSql('DROP INDEX IDX_D5AB8FF5B03A8386 ON patient_medical_consents');
        $this->addSql('ALTER TABLE patient_medical_consents DROP created_by_id');
        $this->addSql('ALTER TABLE treatment_prescription_items DROP FOREIGN KEY FK_4F5A21AEB03A8386');
        $this->addSql('DROP INDEX IDX_4F5A21AEB03A8386 ON treatment_prescription_items');
        $this->addSql('ALTER TABLE treatment_prescription_items DROP created_by_id');
    }
}
