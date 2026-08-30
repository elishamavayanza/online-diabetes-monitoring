<?php

namespace App\Service\Healthcare\Report;

use App\DTO\Feedback;
use App\DTO\Response\Healthcare\Report\OrganizationReportVerificationDTO;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use DateTimeImmutable;

final class OrganizationReportVerificationService
{
    public function __construct(
        private readonly HealthcareOrganizationRepository $organizationRepository,
    ) {}

    public function verify(
        ?string $reference,
        ?string $organizationId,
        ?string $from,
        ?string $to
    ): Feedback {
        $feedback = new Feedback();

        if (!$reference || !$organizationId || !$from || !$to) {
            return $feedback
                ->setData($this->invalid('Paramètres de vérification incomplets.'))
                ->setStatus(400)
                ->setErrorFlushDescription('Paramètres de vérification incomplets.')
                ->autoInitFlush();
        }

        if (!preg_match('/^RPT-ORG-(\d+)-(\d{8})$/', $reference, $matches)) {
            return $feedback
                ->setData($this->invalid('Référence de rapport invalide.'))
                ->setStatus(400)
                ->setErrorFlushDescription('Référence de rapport invalide.')
                ->autoInitFlush();
        }

        $referenceOrganizationId = $matches[1];
        $referencePeriodEnd = $matches[2];

        if ($referenceOrganizationId !== $organizationId) {
            return $feedback
                ->setData($this->invalid('La référence ne correspond pas à l\'organisation indiquée.'))
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

        $organization = $this->organizationRepository->find($organizationId);
        if (!$organization) {
            return $feedback
                ->setData($this->invalid('Organisation introuvable.'))
                ->setStatus(404)
                ->setErrorFlushDescription('Organisation introuvable.')
                ->autoInitFlush();
        }

        $verification = new OrganizationReportVerificationDTO(
            authentic: true,
            reference: $reference,
            organizationId: (string) $organization->getId(),
            organizationName: $organization->getName() ?? 'Organisation',
            periodFrom: $from,
            periodTo: $to,
            documentType: 'Rapport analytique organisationnel DiabCare',
            verifiedAt: (new DateTimeImmutable())->format(DATE_ATOM),
            message: 'Ce document correspond à un rapport officiel généré par la plateforme DiabCare pour cette organisation.',
        );

        return $feedback
            ->setData($verification)
            ->setFlushDescription('Rapport authentique.')
            ->autoInitFlush();
    }

    private function invalid(string $message): OrganizationReportVerificationDTO
    {
        return new OrganizationReportVerificationDTO(
            authentic: false,
            reference: '',
            organizationId: '',
            organizationName: '',
            periodFrom: '',
            periodTo: '',
            documentType: 'Rapport analytique organisationnel DiabCare',
            verifiedAt: (new DateTimeImmutable())->format(DATE_ATOM),
            message: $message,
        );
    }
}
