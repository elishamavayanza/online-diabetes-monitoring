<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260811145344_V2 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE communication_conversations (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, closed_at DATETIME DEFAULT NULL, patient_id BIGINT UNSIGNED NOT NULL, organization_id BIGINT UNSIGNED DEFAULT NULL, INDEX IDX_6F4409526B899279 (patient_id), INDEX IDX_6F44095232C8A3DE (organization_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE communication_message_attachments (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, file_url VARCHAR(500) NOT NULL, file_name VARCHAR(255) NOT NULL, mime_type VARCHAR(150) NOT NULL, size_bytes INT NOT NULL, message_id BIGINT UNSIGNED NOT NULL, INDEX IDX_6C1C5FD537A1329 (message_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE communication_message_read_receipts (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, read_at DATETIME NOT NULL, message_id BIGINT UNSIGNED NOT NULL, user_id BIGINT UNSIGNED NOT NULL, INDEX IDX_394F5A9A537A1329 (message_id), INDEX IDX_394F5A9AA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE communication_messages (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, content LONGTEXT NOT NULL, sent_at DATETIME NOT NULL, edited_at DATETIME DEFAULT NULL, conversation_id BIGINT UNSIGNED NOT NULL, sender_id BIGINT UNSIGNED NOT NULL, INDEX IDX_50C977559AC0396 (conversation_id), INDEX IDX_50C97755F624B39D (sender_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE communication_conversations ADD CONSTRAINT FK_6F4409526B899279 FOREIGN KEY (patient_id) REFERENCES identity_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE communication_conversations ADD CONSTRAINT FK_6F44095232C8A3DE FOREIGN KEY (organization_id) REFERENCES healthcare_organizations (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE communication_message_attachments ADD CONSTRAINT FK_6C1C5FD537A1329 FOREIGN KEY (message_id) REFERENCES communication_messages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE communication_message_read_receipts ADD CONSTRAINT FK_394F5A9A537A1329 FOREIGN KEY (message_id) REFERENCES communication_messages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE communication_message_read_receipts ADD CONSTRAINT FK_394F5A9AA76ED395 FOREIGN KEY (user_id) REFERENCES identity_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE communication_messages ADD CONSTRAINT FK_50C977559AC0396 FOREIGN KEY (conversation_id) REFERENCES communication_conversations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE communication_messages ADD CONSTRAINT FK_50C97755F624B39D FOREIGN KEY (sender_id) REFERENCES identity_users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE communication_conversations DROP FOREIGN KEY FK_6F4409526B899279');
        $this->addSql('ALTER TABLE communication_conversations DROP FOREIGN KEY FK_6F44095232C8A3DE');
        $this->addSql('ALTER TABLE communication_message_attachments DROP FOREIGN KEY FK_6C1C5FD537A1329');
        $this->addSql('ALTER TABLE communication_message_read_receipts DROP FOREIGN KEY FK_394F5A9A537A1329');
        $this->addSql('ALTER TABLE communication_message_read_receipts DROP FOREIGN KEY FK_394F5A9AA76ED395');
        $this->addSql('ALTER TABLE communication_messages DROP FOREIGN KEY FK_50C977559AC0396');
        $this->addSql('ALTER TABLE communication_messages DROP FOREIGN KEY FK_50C97755F624B39D');
        $this->addSql('DROP TABLE communication_conversations');
        $this->addSql('DROP TABLE communication_message_attachments');
        $this->addSql('DROP TABLE communication_message_read_receipts');
        $this->addSql('DROP TABLE communication_messages');
    }
}
