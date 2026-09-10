<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/** Associe les médicaments nouvellement créés à leur organisation propriétaire. */
final class Version20260910090000_AddMedicationOrganization extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute le propriétaire organisationnel des médicaments pour isoler les catalogues.';
    }

    public function up(Schema $schema): void
    {
        // Nullable pour préserver les médicaments historiques : ils restent visibles uniquement au ROOT.
        $this->addSql('ALTER TABLE treatment_medications ADD organization_id BIGINT UNSIGNED DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_TREATMENT_MEDICATION_ORGANIZATION ON treatment_medications (organization_id)');
        $this->addSql('ALTER TABLE treatment_medications ADD CONSTRAINT FK_TREATMENT_MEDICATION_ORGANIZATION FOREIGN KEY (organization_id) REFERENCES healthcare_organizations (id) ON DELETE RESTRICT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE treatment_medications DROP FOREIGN KEY FK_TREATMENT_MEDICATION_ORGANIZATION');
        $this->addSql('DROP INDEX IDX_TREATMENT_MEDICATION_ORGANIZATION ON treatment_medications');
        $this->addSql('ALTER TABLE treatment_medications DROP organization_id');
    }
}
