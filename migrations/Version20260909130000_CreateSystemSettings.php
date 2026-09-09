<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Crée la table des paramètres globaux de la plateforme (configuration ROOT) :
 * identité visuelle (nom, logo) et textes de la page d'accueil publique.
 */
final class Version20260909130000_CreateSystemSettings extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crée la table system_settings (configuration système pour le ROOT).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE system_settings (
                id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
                system_name VARCHAR(150) NOT NULL,
                logo_url VARCHAR(500) DEFAULT NULL,
                hero_title TEXT DEFAULT NULL,
                hero_subtitle TEXT DEFAULT NULL,
                about_title TEXT DEFAULT NULL,
                about_content TEXT DEFAULT NULL,
                features_title TEXT DEFAULT NULL,
                features JSON DEFAULT NULL,
                users_title TEXT DEFAULT NULL,
                users JSON DEFAULT NULL,
                cta_title TEXT DEFAULT NULL,
                cta_subtitle TEXT DEFAULT NULL,
                footer_tagline TEXT DEFAULT NULL,
                footer_copyright TEXT DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME DEFAULT NULL,
                deleted_at DATETIME DEFAULT NULL,
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE system_settings');
    }
}