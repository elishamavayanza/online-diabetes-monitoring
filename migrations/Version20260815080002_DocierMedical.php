<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260815080002_DocierMedical extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medical_medical_records DROP FOREIGN KEY `FK_2C684778BB9D6FEE`');
        $this->addSql('DROP INDEX IDX_2C684778BB9D6FEE ON medical_medical_records');
        $this->addSql('ALTER TABLE medical_medical_records DROP measured_at, DROP source, DROP notes, DROP issuer_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medical_medical_records ADD measured_at DATETIME NOT NULL, ADD source VARCHAR(50) DEFAULT NULL, ADD notes LONGTEXT DEFAULT NULL, ADD issuer_id BIGINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE medical_medical_records ADD CONSTRAINT `FK_2C684778BB9D6FEE` FOREIGN KEY (issuer_id) REFERENCES identity_users (id)');
        $this->addSql('CREATE INDEX IDX_2C684778BB9D6FEE ON medical_medical_records (issuer_id)');
    }
}
