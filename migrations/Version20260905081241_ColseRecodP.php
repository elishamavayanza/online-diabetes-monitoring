<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260905081241_ColseRecodP extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_IDENTITY_USERS_REFRESH_TOKEN_HASH ON identity_users');
        $this->addSql('ALTER TABLE medical_medical_records ADD closure_reason LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX IDX_IDENTITY_USERS_REFRESH_TOKEN_HASH ON identity_users (refresh_token_hash)');
        $this->addSql('ALTER TABLE medical_medical_records DROP closure_reason');
    }
}
