<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Restaure la forme des médicaments généraux créés ou modifiés avant la
 * correction qui évitait de supprimer ce champ.
 */
final class Version20260910100000_RestoreGeneralMedicationForms extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Restaure la forme TABLET des médicaments généraux sans forme.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE treatment_medications SET form = 'TABLET' WHERE category = 'GENERAL' AND form IS NULL");
    }

    public function down(Schema $schema): void
    {
        // Le rattrapage ne peut pas être inversé sans effacer des formes légitimes.
    }
}
