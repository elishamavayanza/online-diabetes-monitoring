<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Index composites pour l'historique des mesures, repas et injections
 * (filtres patient + date utilisés par les listes et le dossier agrégé).
 */
final class Version20260910100000_AddMeasurementMealPerformanceIndexes extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les index (patient_id, measured_at/injected_at) pour mesures, repas et injections.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX idx_bgm_patient_measured ON medical_blood_glucose_measurements (patient_id, measured_at)');
        $this->addSql('CREATE INDEX idx_bpm_patient_measured ON medical_blood_pressure_measurements (patient_id, measured_at)');
        $this->addSql('CREATE INDEX idx_hba_patient_measured ON medical_hba1c_measurements (patient_id, measured_at)');
        $this->addSql('CREATE INDEX idx_wm_patient_measured ON medical_weight_measurements (patient_id, measured_at)');
        $this->addSql('CREATE INDEX idx_pam_patient_measured ON medical_physical_activity_measurements (patient_id, measured_at)');
        $this->addSql('CREATE INDEX idx_lr_patient_measured ON medical_laboratory_results (patient_id, measured_at)');
        $this->addSql('CREATE INDEX idx_meal_patient_measured ON nutrition_meals (patient_id, measured_at)');
        $this->addSql('CREATE INDEX idx_ii_patient_injected ON treatment_insulin_injections (patient_id, injected_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_ii_patient_injected ON treatment_insulin_injections');
        $this->addSql('DROP INDEX idx_meal_patient_measured ON nutrition_meals');
        $this->addSql('DROP INDEX idx_lr_patient_measured ON medical_laboratory_results');
        $this->addSql('DROP INDEX idx_pam_patient_measured ON medical_physical_activity_measurements');
        $this->addSql('DROP INDEX idx_wm_patient_measured ON medical_weight_measurements');
        $this->addSql('DROP INDEX idx_hba_patient_measured ON medical_hba1c_measurements');
        $this->addSql('DROP INDEX idx_bpm_patient_measured ON medical_blood_pressure_measurements');
        $this->addSql('DROP INDEX idx_bgm_patient_measured ON medical_blood_glucose_measurements');
    }
}
