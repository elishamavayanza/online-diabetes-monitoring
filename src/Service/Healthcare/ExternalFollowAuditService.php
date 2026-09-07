<?php

namespace App\Service\Healthcare;

use App\Entity\Healthcare\ExternalFollowInvitation;
use App\Entity\Healthcare\ExternalFollowLog;
use App\Entity\Healthcare\ExternalFollowStatus;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Identity\Patient;
use App\Repository\Healthcare\ExternalFollowInvitationRepository;
use App\Security\SecurityAction;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Enregistre dans le dossier patient les actions réalisées par un
 * professionnel externe invité à suivre ce patient.
 */
class ExternalFollowAuditService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ExternalFollowInvitationRepository $invitationRepository
    ) {}

    /**
     * Retrouve l'invitation en cours de validité liant ce professionnel
     * à ce patient (état ACCEPTED, délai non échu).
     */
    public function findActiveInvitation(
        Patient $patient,
        HealthcareProfessional $professional
    ): ?ExternalFollowInvitation {
        $today = new \DateTimeImmutable('today');

        return $this->invitationRepository->createQueryBuilder('i')
            ->andWhere('i.patient = :patient')
            ->andWhere('i.professional = :professional')
            ->andWhere('i.status = :accepted')
            ->andWhere('(i.endDate IS NULL OR i.endDate >= :today)')
            ->andWhere('i.deletedAt IS NULL')
            ->setParameter('patient', $patient)
            ->setParameter('professional', $professional)
            ->setParameter('accepted', ExternalFollowStatus::ACCEPTED)
            ->setParameter('today', $today)
            ->orderBy('i.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Consigne une action dans le journal du suivi externe.
     * Les actions de lecture (consultations) peuvent être volontairement
     * ignorées pour ne consigner que les actions réellement effectuées.
     */
    public function record(
        Patient $patient,
        HealthcareProfessional $professional,
        ExternalFollowInvitation $invitation,
        SecurityAction $action,
        ?string $detail = null
    ): void {
        if ($this->isReadOnlyAction($action)) {
            return;
        }

        $log = new ExternalFollowLog();
        $log->setPatient($patient);
        $log->setProfessional($professional);
        $log->setOrganization($invitation->getOrganization());
        $log->setInvitation($invitation);
        $log->setAction($action->value);
        $log->setActionLabel($this->labelFor($action));
        $log->setDetail($detail);

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    public function isReadOnlyAction(SecurityAction $action): bool
    {
        $readOnly = [
            SecurityAction::VIEW,
            SecurityAction::VIEW_PATIENT,
            SecurityAction::VIEW_MEDICAL_RECORD,
            SecurityAction::VIEW_MEDICAL_NOTES,
            SecurityAction::VIEW_MEASUREMENTS,
            SecurityAction::VIEW_INSULIN_INJECTION,
            SecurityAction::VIEW_LABORATORY_RESULT,
            SecurityAction::VIEW_PRESCRIPTION,
            SecurityAction::VIEW_NUTRITION,
            SecurityAction::VIEW_ALLERGY,
            SecurityAction::VIEW_EMERGENCY_CONTACT,
            SecurityAction::VIEW_MEDICAL_CONSENT,
            SecurityAction::VIEW_MEDICATION,
        ];

        return in_array($action, $readOnly, true);
    }

    private function labelFor(SecurityAction $action): string
    {
        $labels = [
            SecurityAction::CREATE_MEDICAL_NOTE->value => 'Ajout d\'une note médicale',
            SecurityAction::EDIT_MEDICAL_NOTE->value => 'Modification d\'une note médicale',
            SecurityAction::DELETE_MEDICAL_NOTE->value => 'Suppression d\'une note médicale',
            SecurityAction::CREATE_DIAGNOSIS->value => 'Ajout d\'un diagnostic',
            SecurityAction::UPDATE_DIAGNOSIS->value => 'Modification d\'un diagnostic',
            SecurityAction::RECORD_GLUCOSE->value => 'Saisie d\'une mesure de glycémie',
            SecurityAction::RECORD_BLOOD_PRESSURE->value => 'Saisie d\'une mesure de tension artérielle',
            SecurityAction::RECORD_HBA1C->value => 'Saisie du taux d\'HbA1c',
            SecurityAction::RECORD_WEIGHT->value => 'Saisie du poids',
            SecurityAction::RECORD_ACTIVITY->value => 'Saisie d\'une activité',
            SecurityAction::CREATE_ALLERGY->value => 'Ajout d\'une allergie',
            SecurityAction::UPDATE_ALLERGY->value => 'Modification d\'une allergie',
            SecurityAction::DELETE_ALLERGY->value => 'Suppression d\'une allergie',
        ];

        return $labels[$action->value] ?? 'Action sur le dossier';
    }
}