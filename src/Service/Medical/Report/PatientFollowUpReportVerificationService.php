<?php

namespace App\Service\Medical\Report;

use App\DTO\Feedback;
use App\DTO\Response\Medical\Report\PatientFollowUpReportVerificationDTO;
use App\Repository\Identity\PatientRepository;
use DateTimeImmutable;

final class PatientFollowUpReportVerificationService
{
    public function __construct(
        private readonly PatientRepository $patientRepository,
    ) {}

    public function verify(
        ?string $reference,
        ?string $patientId,
        ?string $from,
        ?string $to
    ): Feedback {
        $feedback = new Feedback();

        if (!$reference || !$patientId || !$from || !$to) {
            return $feedback
                ->setData($this->invalid('Paramètres de vérification incomplets.'))
                ->setStatus(400)
                ->setErrorFlushDescription('Paramètres de vérification incomplets.')
                ->autoInitFlush();
        }

        if (!preg_match('/^RPT-PAT-(\d+)-(\d{8})$/', $reference, $matches)) {
            return $feedback
                ->setData($this->invalid('Référence de rapport invalide.'))
                ->setStatus(400)
                ->setErrorFlushDescription('Référence de rapport invalide.')
                ->autoInitFlush();
        }

        $referencePatientId = $matches[1];
        $referencePeriodEnd = $matches[2];

        if ($referencePatientId !== $patientId) {
            return $feedback
                ->setData($this->invalid('La référence ne correspond pas au patient indiqué.'))
                ->setStatus(404)
                ->setErrorFlushDescription('Rapport non authentique.')
                ->autoInitFlush();
        }

        $normalizedTo = str_replace('-', '', $to);
        if ($referencePeriodEnd !== $normalizedTo) {
            return $feedback
                ->setData($this->invalid('La référence ne correspond pas à la période indiquée.'))
                ->setStatus(404)
                ->setErrorFlushDescription('Rapport non authentique.')
                ->autoInitFlush();
        }

        $fromDate = DateTimeImmutable::createFromFormat('Y-m-d', $from);
        $toDate = DateTimeImmutable::createFromFormat('Y-m-d', $to);

        if (!$fromDate || !$toDate || $fromDate > $toDate) {
            return $feedback
                ->setData($this->invalid('Période du rapport invalide.'))
                ->setStatus(400)
                ->setErrorFlushDescription('Période du rapport invalide.')
                ->autoInitFlush();
        }

        $patient = $this->patientRepository->find($patientId);
        if (!$patient) {
            return $feedback
                ->setData($this->invalid('Patient introuvable.'))
                ->setStatus(404)
                ->setErrorFlushDescription('Patient introuvable.')
                ->autoInitFlush();
        }

        $organizationName = null;
        foreach ($patient->getOrganizationMemberships() as $membership) {
            if ($membership->getStatus()?->isActive() && $membership->getOrganization() !== null) {
                $organizationName = $membership->getOrganization()->getName();
                break;
            }
        }

        $verification = new PatientFollowUpReportVerificationDTO(
            authentic: true,
            reference: $reference,
            patientId: (string) $patient->getId(),
            patientFullName: $patient->getFullName(),
            organizationName: $organizationName,
            periodFrom: $from,
            periodTo: $to,
            documentType: 'Rapport périodique d\'évolution patient DiabCare',
            verifiedAt: (new DateTimeImmutable())->format(DATE_ATOM),
            message: 'Ce document correspond à un rapport officiel généré par la plateforme DiabCare pour ce patient.',
        );

        return $feedback
            ->setData($verification)
            ->setFlushDescription('Rapport authentique.')
            ->autoInitFlush();
    }

    private function invalid(string $message): PatientFollowUpReportVerificationDTO
    {
        return new PatientFollowUpReportVerificationDTO(
            authentic: false,
            reference: '',
            patientId: '',
            patientFullName: '',
            organizationName: null,
            periodFrom: '',
            periodTo: '',
            documentType: 'Rapport périodique d\'évolution patient DiabCare',
            verifiedAt: (new DateTimeImmutable())->format(DATE_ATOM),
            message: $message,
        );
    }
}
