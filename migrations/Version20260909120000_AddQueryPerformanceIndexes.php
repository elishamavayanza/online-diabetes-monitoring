<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajoute les index composites nécessaires aux requêtes les plus sollicitées
 * (listes paginées, messagerie, notifications, plannings) pour supprimer
 * les scans séquentiels sur les tables volumineuses.
 */
final class Version20260909120000_AddQueryPerformanceIndexes extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les index composites de performance (search, tri, messagerie, notifications).';
    }

    public function up(Schema $schema): void
    {
        // Membership : filtre ROOT/org par statut (UserListQueryTrait)
        $this->addSql('CREATE INDEX idx_om_user_org ON healthcare_organization_memberships (user_id, organization_id)');
        $this->addSql('CREATE INDEX idx_om_org_status ON healthcare_organization_memberships (organization_id, status)');

        // Messagerie : liste d'une conversation triée par date
        $this->addSql('CREATE INDEX idx_msg_conv_created ON communication_messages (conversation_id, created_at)');
        // Accusés de lecture : lookups par couple message/user
        $this->addSql('CREATE INDEX idx_rr_msg_user ON communication_message_read_receipts (message_id, user_id)');

        // Rendez-vous : recherche par acteur puis par créneau
        $this->addSql('CREATE INDEX idx_appt_org_status_start ON appointment_appointments (organization_id, status, scheduled_at)');
        $this->addSql('CREATE INDEX idx_appt_prof_start ON appointment_appointments (professional_id, scheduled_at)');
        $this->addSql('CREATE INDEX idx_appt_patient_start ON appointment_appointments (patient_id, scheduled_at)');

        // Notifications : boîte de notification par user, non lues en premier
        $this->addSql('CREATE INDEX idx_notif_user_read ON notification_notifications (user_id, read_at)');

        // Suivis externes : listes actives par acteur/org
        $this->addSql('CREATE INDEX idx_efi_prof_status ON external_follow_invitations (professional_id, status)');
        $this->addSql('CREATE INDEX idx_efi_org_status ON external_follow_invitations (organization_id, status)');
        $this->addSql('CREATE INDEX idx_efi_patient_status ON external_follow_invitations (patient_id, status)');

        // Équipe de soins : assignments actifs par acteur
        $this->addSql('CREATE INDEX idx_cta_prof_active ON healthcare_care_team_assignments (professional_id, active)');
        $this->addSql('CREATE INDEX idx_cta_patient_active ON healthcare_care_team_assignments (patient_id, active)');
        $this->addSql('CREATE INDEX idx_cta_org_active ON healthcare_care_team_assignments (organization_id, active)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_cta_org_active ON healthcare_care_team_assignments');
        $this->addSql('DROP INDEX idx_cta_patient_active ON healthcare_care_team_assignments');
        $this->addSql('DROP INDEX idx_cta_prof_active ON healthcare_care_team_assignments');
        $this->addSql('DROP INDEX idx_efi_patient_status ON external_follow_invitations');
        $this->addSql('DROP INDEX idx_efi_org_status ON external_follow_invitations');
        $this->addSql('DROP INDEX idx_efi_prof_status ON external_follow_invitations');
        $this->addSql('DROP INDEX idx_notif_user_read ON notification_notifications');
        $this->addSql('DROP INDEX idx_appt_patient_start ON appointment_appointments');
        $this->addSql('DROP INDEX idx_appt_prof_start ON appointment_appointments');
        $this->addSql('DROP INDEX idx_appt_org_status_start ON appointment_appointments');
        $this->addSql('DROP INDEX idx_rr_msg_user ON communication_message_read_receipts');
        $this->addSql('DROP INDEX idx_msg_conv_created ON communication_messages');
        $this->addSql('DROP INDEX idx_om_org_status ON healthcare_organization_memberships');
        $this->addSql('DROP INDEX idx_om_user_org ON healthcare_organization_memberships');
    }
}