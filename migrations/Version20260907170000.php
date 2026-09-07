<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajoute la forme galénique (comprimé/liquide) et le statut actif aux médicaments.
 */
final class Version20260907170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des colonnes form et active sur treatment_medications.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE treatment_medications ADD active TINYINT(1) NOT NULL DEFAULT 1');
        $this->addSql('ALTER TABLE treatment_medications CHANGE active active TINYINT NOT NULL');
        $this->addSql('ALTER TABLE treatment_medications ADD form VARCHAR(50) DEFAULT NULL');
        $this->addSql("UPDATE treatment_medications SET form = 'TABLET' WHERE category = 'GENERAL' AND form IS NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE treatment_medications DROP COLUMN form');
        $this->addSql('ALTER TABLE treatment_medications DROP COLUMN active');
    }
}