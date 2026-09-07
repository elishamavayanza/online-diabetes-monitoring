<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Distingue les classes de médicaments : INSULIN vs GENERAL.
 * - Migre les anciennes valeurs TABLET / OTHER vers GENERAL.
 * - Supprime la colonne insulin_level (remplacée par le profil Insulin dédié).
 */
final class Version20260907150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Revert des catégories TABLET/OTHER vers GENERAL et suppression de insulin_level.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE treatment_medications SET category = 'GENERAL' WHERE category IN ('TABLET', 'OTHER')");
        $this->addSql('ALTER TABLE treatment_medications DROP COLUMN insulin_level');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE treatment_medications ADD insulin_level INT DEFAULT NULL');
        $this->addSql("UPDATE treatment_medications SET category = 'TABLET' WHERE category = 'GENERAL'");
    }
}