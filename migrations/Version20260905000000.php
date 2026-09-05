<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260905000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute le stockage sécurisé des refresh tokens utilisateur.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE identity_users ADD refresh_token_hash VARCHAR(64) DEFAULT NULL, ADD refresh_token_expires_at DATETIME DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_IDENTITY_USERS_REFRESH_TOKEN_HASH ON identity_users (refresh_token_hash)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_IDENTITY_USERS_REFRESH_TOKEN_HASH ON identity_users');
        $this->addSql('ALTER TABLE identity_users DROP refresh_token_hash, DROP refresh_token_expires_at');
    }
}
