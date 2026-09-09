<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260909075812_CreateAccountSuspensions extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crée la table des suspensions de comptes et d’organisations.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE account_suspensions (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, scope VARCHAR(20) NOT NULL, reason VARCHAR(500) NOT NULL, starts_at DATETIME NOT NULL, ends_at DATETIME DEFAULT NULL, canceled_at DATETIME DEFAULT NULL, organization_id BIGINT UNSIGNED DEFAULT NULL, user_id BIGINT UNSIGNED DEFAULT NULL, created_by_id BIGINT UNSIGNED DEFAULT NULL, INDEX IDX_CACB9C0D32C8A3DE (organization_id), INDEX IDX_CACB9C0DA76ED395 (user_id), INDEX IDX_CACB9C0DB03A8386 (created_by_id), INDEX idx_suspension_scope_dates (scope, canceled_at, ends_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE account_suspensions ADD CONSTRAINT FK_CACB9C0D32C8A3DE FOREIGN KEY (organization_id) REFERENCES healthcare_organizations (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE account_suspensions ADD CONSTRAINT FK_CACB9C0DA76ED395 FOREIGN KEY (user_id) REFERENCES identity_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE account_suspensions ADD CONSTRAINT FK_CACB9C0DB03A8386 FOREIGN KEY (created_by_id) REFERENCES identity_users (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE account_suspensions DROP FOREIGN KEY FK_CACB9C0D32C8A3DE');
        $this->addSql('ALTER TABLE account_suspensions DROP FOREIGN KEY FK_CACB9C0DA76ED395');
        $this->addSql('ALTER TABLE account_suspensions DROP FOREIGN KEY FK_CACB9C0DB03A8386');
        $this->addSql('DROP TABLE account_suspensions');
    }
}