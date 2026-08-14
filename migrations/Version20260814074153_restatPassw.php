<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260814074153_RestatPassw extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_707E3809EC7E7152 ON identity_healthcare_professionals');
        $this->addSql('ALTER TABLE identity_healthcare_professionals CHANGE license_number license_number VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE identity_users ADD reset_token VARCHAR(255) DEFAULT NULL, ADD reset_token_expires_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE identity_healthcare_professionals CHANGE license_number license_number VARCHAR(100) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_707E3809EC7E7152 ON identity_healthcare_professionals (license_number)');
        $this->addSql('ALTER TABLE identity_users DROP reset_token, DROP reset_token_expires_at');
    }
}
