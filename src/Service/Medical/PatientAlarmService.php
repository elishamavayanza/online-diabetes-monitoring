<?php

namespace App\Service\Medical;

use App\Entity\Identity\Patient;
use App\Entity\Medical\BloodGlucoseMeasurement;
use App\Entity\Medical\BloodPressureMeasurement;
use App\Entity\Medical\GlucoseUnit;
use App\Entity\Medical\HbA1cMeasurement;
use App\Entity\Notification\Notification;
use App\Entity\Notification\NotificationType;
use App\Entity\Appointment\ReminderChannel;
use App\Repository\Notification\NotificationRepository;
use App\Service\Notification\NotificationService;

/**
 * Détecte automatiquement les mesures anormales et déclenche une
 * alarme (notification + email) pour le patient concerné.
 *
 * Les seuils reprennent ceux affichés en « À surveiller » :
 *  - Glycémie : > 180 mg/dL (10,0 mmol/L) = élevée, < 70 mg/dL (3,9 mmol/L) = basse
 *  - Tension   : systolique >= 135 et/ou diastolique >= 85 mmHg
 *  - HbA1c     : > 7 %
 */
class PatientAlarmService
{
    private const GLUCOSE_HIGH_MGDL = 180.0;
    private const GLUCOSE_LOW_MGDL = 70.0;
    private const GLUCOSE_HIGH_MMOL = 10.0;
    private const GLUCOSE_LOW_MMOL = 3.9;
    private const BP_SYSTOLIC_THRESHOLD = 135.0;
    private const BP_DIASTOLIC_THRESHOLD = 85.0;
    private const HBA1C_THRESHOLD = 7.0;

    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly NotificationRepository $notificationRepository
    ) {}

    /**
     * Évalue une mesure de glycémie et déclenche une alerte si besoin.
     */
    public function evaluateBloodGlucose(BloodGlucoseMeasurement $measurement): ?Notification
    {
        $patient = $measurement->getPatient();
        $value = $measurement->getValue();
        if (!$patient || $value === null) {
            return null;
        }

        $valueFloat = (float) $value;
        $isHigh = $measurement->getUnit() === GlucoseUnit::MMOL_L
            ? $valueFloat > self::GLUCOSE_HIGH_MMOL
            : $valueFloat > self::GLUCOSE_HIGH_MGDL;
        $isLow = $measurement->getUnit() === GlucoseUnit::MMOL_L
            ? $valueFloat < self::GLUCOSE_LOW_MMOL
            : $valueFloat < self::GLUCOSE_LOW_MGDL;

        if (!$isHigh && !$isLow) {
            return null;
        }

        $unitLabel = $measurement->getUnit() === GlucoseUnit::MMOL_L ? 'mmol/L' : 'mg/dL';

        if ($isLow) {
            $title = 'Glycémie basse détectée';
            $body = sprintf(
                'Votre glycémie est basse (%s %s). Si vous souffrez d\'hypoglycémie, prenez une collation sucrée et surveillez votre ressenti. Parlez-en à votre équipe soignante.',
                $this->formatNumber($valueFloat),
                $unitLabel
            );
        } else {
            $title = 'Glycémie élevée détectée';
            $body = sprintf(
                'Votre glycémie est élevée (%s %s). Ce pic peut mériter l\'attention de votre équipe soignante. Pensez à noter le contexte de cette mesure.',
                $this->formatNumber($valueFloat),
                $unitLabel
            );
        }

        return $this->trigger($patient, $title, $body, BloodGlucoseMeasurement::class, (string) $measurement->getId());
    }

    /**
     * Évalue une mesure de tension artérielle et déclenche une alerte si besoin.
     */
    public function evaluateBloodPressure(BloodPressureMeasurement $measurement): ?Notification
    {
        $patient = $measurement->getPatient();
        $systolic = $measurement->getSystolic();
        $diastolic = $measurement->getDiastolic();
        if (!$patient || $systolic === null || $diastolic === null) {
            return null;
        }

        $sys = (float) $systolic;
        $dia = (float) $diastolic;
        if ($sys < self::BP_SYSTOLIC_THRESHOLD && $dia < self::BP_DIASTOLIC_THRESHOLD) {
            return null;
        }

        $title = 'Tension artérielle élevée';
        $body = sprintf(
            'Votre tension artérielle est élevée (%s/%s mmHg). Nous vous recommandons de contrôler à nouveau votre tension dans le calme et d\'en parler à votre professionnel de santé.',
            $this->formatNumber($sys),
            $this->formatNumber($dia)
        );

        return $this->trigger($patient, $title, $body, BloodPressureMeasurement::class, (string) $measurement->getId());
    }

    /**
     * Évalue une mesure d'HbA1c et déclenche une alerte si besoin.
     */
    public function evaluateHbA1c(HbA1cMeasurement $measurement): ?Notification
    {
        $patient = $measurement->getPatient();
        $value = $measurement->getValuePercent();
        if (!$patient || $value === null) {
            return null;
        }

        $valueFloat = (float) $value;
        if ($valueFloat <= self::HBA1C_THRESHOLD) {
            return null;
        }

        $title = 'HbA1c au-dessus de l\'objectif';
        $body = sprintf(
            'Votre taux d\'HbA1c est de %s %%, au-dessus de l\'objectif de %s %%. Parlez-en avec votre équipe soignante afin d\'ajuster votre traitement si nécessaire.',
            $this->formatNumber($valueFloat),
            $this->formatNumber(self::HBA1C_THRESHOLD)
        );

        return $this->trigger($patient, $title, $body, HbA1cMeasurement::class, (string) $measurement->getId());
    }

    private function trigger(
        Patient $patient,
        string $title,
        string $body,
        string $relatedEntityType,
        string $relatedEntityId
    ): ?Notification {
        // Anti-doublon : une seule alerte par mesure.
        $existing = $this->notificationRepository->findOneBy([
            'user' => $patient,
            'relatedEntityType' => $relatedEntityType,
            'relatedEntityId' => $relatedEntityId,
        ]);
        if ($existing) {
            return null;
        }

        return $this->notificationService->createDirect(
            $patient,
            NotificationType::SYSTEM_ALERT,
            $title,
            $body,
            ReminderChannel::EMAIL,
            $relatedEntityType,
            $relatedEntityId
        );
    }

    private function formatNumber(float $value): string
    {
        return rtrim(rtrim(number_format($value, 1, ',', ''), '0'), ',');
    }
}