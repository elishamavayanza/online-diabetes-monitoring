<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260812103859_V3 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de address_state sur les tables avec une adresse';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE healthcare_facilities ADD address_state VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE healthcare_organizations ADD address_state VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE identity_users ADD address_state VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE healthcare_facilities DROP address_state');
        $this->addSql('ALTER TABLE healthcare_organizations DROP address_state');
        $this->addSql('ALTER TABLE identity_users DROP address_state');
    }
}
