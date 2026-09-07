<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260907101449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE treatment_insulin_injections (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, injected_at DATETIME NOT NULL, dose_units NUMERIC(8, 2) NOT NULL, injection_site VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, notes LONGTEXT DEFAULT NULL, patient_id BIGINT UNSIGNED NOT NULL, prescription_item_id BIGINT UNSIGNED NOT NULL, insulin_id BIGINT UNSIGNED NOT NULL, issuer_id BIGINT UNSIGNED NOT NULL, INDEX IDX_7B8ED8536B899279 (patient_id), INDEX IDX_7B8ED85365C37E20 (prescription_item_id), INDEX IDX_7B8ED8536FBB320A (insulin_id), INDEX IDX_7B8ED853BB9D6FEE (issuer_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE treatment_insulins (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, insulin_type VARCHAR(50) NOT NULL, concentration VARCHAR(50) NOT NULL, medication_id BIGINT UNSIGNED NOT NULL, INDEX IDX_5BA5D5732C4DE6DA (medication_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE treatment_insulin_injections ADD CONSTRAINT FK_7B8ED8536B899279 FOREIGN KEY (patient_id) REFERENCES identity_patients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE treatment_insulin_injections ADD CONSTRAINT FK_7B8ED85365C37E20 FOREIGN KEY (prescription_item_id) REFERENCES treatment_prescription_items (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE treatment_insulin_injections ADD CONSTRAINT FK_7B8ED8536FBB320A FOREIGN KEY (insulin_id) REFERENCES treatment_insulins (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE treatment_insulin_injections ADD CONSTRAINT FK_7B8ED853BB9D6FEE FOREIGN KEY (issuer_id) REFERENCES identity_users (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE treatment_insulins ADD CONSTRAINT FK_5BA5D5732C4DE6DA FOREIGN KEY (medication_id) REFERENCES treatment_medications (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE treatment_insulin_injections DROP FOREIGN KEY FK_7B8ED8536B899279');
        $this->addSql('ALTER TABLE treatment_insulin_injections DROP FOREIGN KEY FK_7B8ED85365C37E20');
        $this->addSql('ALTER TABLE treatment_insulin_injections DROP FOREIGN KEY FK_7B8ED8536FBB320A');
        $this->addSql('ALTER TABLE treatment_insulin_injections DROP FOREIGN KEY FK_7B8ED853BB9D6FEE');
        $this->addSql('ALTER TABLE treatment_insulins DROP FOREIGN KEY FK_5BA5D5732C4DE6DA');
        $this->addSql('DROP TABLE treatment_insulin_injections');
        $this->addSql('DROP TABLE treatment_insulins');
    }
}
