<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Tables des invitations de suivi externe et du journal des actions.
 */
final class Version20260907122555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création des tables external_follow_invitations et external_follow_logs.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE external_follow_invitations (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, email VARCHAR(255) NOT NULL, token VARCHAR(64) NOT NULL, status VARCHAR(20) NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, message LONGTEXT DEFAULT NULL, accepted_at DATETIME DEFAULT NULL, declined_at DATETIME DEFAULT NULL, revoked_at DATETIME DEFAULT NULL, patient_id BIGINT UNSIGNED NOT NULL, organization_id BIGINT UNSIGNED NOT NULL, invited_by_id BIGINT UNSIGNED DEFAULT NULL, professional_id BIGINT UNSIGNED NOT NULL, assignment_id BIGINT UNSIGNED DEFAULT NULL, UNIQUE INDEX UNIQ_864C54075F37A13B (token), INDEX IDX_864C54076B899279 (patient_id), INDEX IDX_864C540732C8A3DE (organization_id), INDEX IDX_864C5407A7B4A7E3 (invited_by_id), INDEX IDX_864C5407DB77003 (professional_id), UNIQUE INDEX UNIQ_864C5407D19302F8 (assignment_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE external_follow_logs (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, action VARCHAR(100) NOT NULL, action_label VARCHAR(255) DEFAULT NULL, detail LONGTEXT DEFAULT NULL, patient_id BIGINT UNSIGNED NOT NULL, professional_id BIGINT UNSIGNED NOT NULL, organization_id BIGINT UNSIGNED NOT NULL, invitation_id BIGINT UNSIGNED DEFAULT NULL, INDEX IDX_A68F78766B899279 (patient_id), INDEX IDX_A68F7876DB77003 (professional_id), INDEX IDX_A68F787632C8A3DE (organization_id), INDEX IDX_A68F7876A35D7AF0 (invitation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE external_follow_invitations ADD CONSTRAINT FK_864C54076B899279 FOREIGN KEY (patient_id) REFERENCES identity_patients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE external_follow_invitations ADD CONSTRAINT FK_864C540732C8A3DE FOREIGN KEY (organization_id) REFERENCES healthcare_organizations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE external_follow_invitations ADD CONSTRAINT FK_864C5407A7B4A7E3 FOREIGN KEY (invited_by_id) REFERENCES identity_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE external_follow_invitations ADD CONSTRAINT FK_864C5407DB77003 FOREIGN KEY (professional_id) REFERENCES identity_healthcare_professionals (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE external_follow_invitations ADD CONSTRAINT FK_864C5407D19302F8 FOREIGN KEY (assignment_id) REFERENCES healthcare_care_team_assignments (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE external_follow_logs ADD CONSTRAINT FK_A68F78766B899279 FOREIGN KEY (patient_id) REFERENCES identity_patients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE external_follow_logs ADD CONSTRAINT FK_A68F7876DB77003 FOREIGN KEY (professional_id) REFERENCES identity_healthcare_professionals (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE external_follow_logs ADD CONSTRAINT FK_A68F787632C8A3DE FOREIGN KEY (organization_id) REFERENCES healthcare_organizations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE external_follow_logs ADD CONSTRAINT FK_A68F7876A35D7AF0 FOREIGN KEY (invitation_id) REFERENCES external_follow_invitations (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE external_follow_invitations DROP FOREIGN KEY FK_864C54076B899279');
        $this->addSql('ALTER TABLE external_follow_invitations DROP FOREIGN KEY FK_864C540732C8A3DE');
        $this->addSql('ALTER TABLE external_follow_invitations DROP FOREIGN KEY FK_864C5407A7B4A7E3');
        $this->addSql('ALTER TABLE external_follow_invitations DROP FOREIGN KEY FK_864C5407DB77003');
        $this->addSql('ALTER TABLE external_follow_invitations DROP FOREIGN KEY FK_864C5407D19302F8');
        $this->addSql('ALTER TABLE external_follow_logs DROP FOREIGN KEY FK_A68F78766B899279');
        $this->addSql('ALTER TABLE external_follow_logs DROP FOREIGN KEY FK_A68F7876DB77003');
        $this->addSql('ALTER TABLE external_follow_logs DROP FOREIGN KEY FK_A68F787632C8A3DE');
        $this->addSql('ALTER TABLE external_follow_logs DROP FOREIGN KEY FK_A68F7876A35D7AF0');
        $this->addSql('DROP TABLE external_follow_invitations');
        $this->addSql('DROP TABLE external_follow_logs');
    }
}
