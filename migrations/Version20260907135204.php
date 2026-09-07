<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260907135204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fermeture du suivi externe par le professionnel : statut CLOSED_BY_PROFESSIONAL, motif et date.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE external_follow_invitations ADD closure_reason LONGTEXT DEFAULT NULL, ADD closed_by_professional_at DATETIME DEFAULT NULL, CHANGE status status VARCHAR(45) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE external_follow_invitations DROP closure_reason, DROP closed_by_professional_at, CHANGE status status VARCHAR(20) NOT NULL');
    }
}
