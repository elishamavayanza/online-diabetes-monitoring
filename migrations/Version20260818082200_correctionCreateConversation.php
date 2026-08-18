<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260818082200_CorrectionCreateConversation extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE communication_conversations ADD created_by_id BIGINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE communication_conversations ADD CONSTRAINT FK_6F440952B03A8386 FOREIGN KEY (created_by_id) REFERENCES identity_users (id)');
        $this->addSql('CREATE INDEX IDX_6F440952B03A8386 ON communication_conversations (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE communication_conversations DROP FOREIGN KEY FK_6F440952B03A8386');
        $this->addSql('DROP INDEX IDX_6F440952B03A8386 ON communication_conversations');
        $this->addSql('ALTER TABLE communication_conversations DROP created_by_id');
    }
}
