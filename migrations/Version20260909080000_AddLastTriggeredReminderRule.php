<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajoute la colonne "last_triggered_at" aux règles de rappel
 * pour éviter les envois en double lors de l'exécution du
 * schedule app:notifications:send-pending.
 */
final class Version20260909080000_AddLastTriggeredReminderRule extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute last_triggered_at à notification_reminder_rules';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification_reminder_rules ADD last_triggered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification_reminder_rules DROP last_triggered_at');
    }
}