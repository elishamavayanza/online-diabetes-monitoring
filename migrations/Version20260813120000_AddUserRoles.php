<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260813120000_AddUserRoles extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les rôles persistants aux utilisateurs et conserve ROLE_ROOT pour les administrateurs existants.';
    }

    public function up(Schema $schema): void
    {
        // 1. Forcer toutes les lignes à un tableau JSON vide par défaut (évite les NULL)
        $this->addSql("UPDATE identity_users SET roles = JSON_ARRAY() WHERE roles IS NULL OR roles = '' OR roles = 'null'");

        // 2. Assigner ROLE_ROOT spécifiquement aux administrateurs
        $this->addSql("UPDATE identity_users SET roles = JSON_ARRAY('ROLE_ROOT') WHERE user_type = 'administrator'");

        // 3. S'assurer que la colonne accepte ou refuse le NOT NULL selon votre souhait
        $this->addSql('ALTER TABLE identity_users MODIFY roles JSON NOT NULL');
    }
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE identity_users DROP roles');
    }
}
