<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/** Isole les catalogues d'aliments par organisation. */
final class Version20260910110000_AddFoodOrganization extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute l’organisation propriétaire aux aliments.';
    }

    public function up(Schema $schema): void
    {
        $sm = $this->connection->createSchemaManager();
        $columns = $sm->listTableColumns('nutrition_foods');

        // Vérifie si la colonne existe déjà pour éviter l'erreur de duplication
        if (!isset($columns['organization_id'])) {
            $this->addSql('ALTER TABLE nutrition_foods ADD organization_id BIGINT UNSIGNED DEFAULT NULL');
        }

        // Vérifie si l'index existe déjà
        $indexes = $sm->listTableIndexes('nutrition_foods');
        if (!isset($indexes['idx_nutrition_food_organization'])) {
            $this->addSql('CREATE INDEX IDX_NUTRITION_FOOD_ORGANIZATION ON nutrition_foods (organization_id)');
        }

        // Vérifie si la contrainte de clé étrangère existe déjà
        $foreignKeys = $sm->listTableForeignKeys('nutrition_foods');
        $fkExists = false;
        foreach ($foreignKeys as $fk) {
            if (strtolower($fk->getName()) === 'fk_nutrition_food_organization') {
                $fkExists = true;
                break;
            }
        }

        if (!$fkExists) {
            $this->addSql('ALTER TABLE nutrition_foods ADD CONSTRAINT FK_NUTRITION_FOOD_ORGANIZATION FOREIGN KEY (organization_id) REFERENCES healthcare_organizations (id) ON DELETE RESTRICT');
        }

        // Reprend les anciens aliments lorsque leur créateur n'appartient qu'à une organisation active.
        $this->addSql("UPDATE nutrition_foods f INNER JOIN healthcare_organization_memberships om ON om.user_id = f.created_by_id AND om.status = 'ACTIVE' LEFT JOIN healthcare_organization_memberships om_other ON om_other.user_id = f.created_by_id AND om_other.status = 'ACTIVE' AND om_other.organization_id <> om.organization_id SET f.organization_id = om.organization_id WHERE f.organization_id IS NULL AND om_other.id IS NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE nutrition_foods DROP FOREIGN KEY FK_NUTRITION_FOOD_ORGANIZATION');
        $this->addSql('DROP INDEX IDX_NUTRITION_FOOD_ORGANIZATION ON nutrition_foods');
        $this->addSql('ALTER TABLE nutrition_foods DROP organization_id');
    }
}
