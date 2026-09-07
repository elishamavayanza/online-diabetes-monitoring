<?php

namespace App\DTO\Response\Treatment;

use App\Entity\Treatment\InsulinInjection;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InsulinInjectionResponseDTO',
    title: 'InsulinInjectionResponseDTO',
    description: 'Structure de réponse pour une injection d’insuline'
)]
class InsulinInjectionResponseDTO
{
    public function __construct(
        #[OA\Property(description: 'Identifiant unique', type: 'string', format: 'uuid', example: '77dd1245-12f4-4b53-8811-7a6543210999')]
        public readonly string $id,

        #[OA\Property(description: 'ID du patient', type: 'string', format: 'uuid', example: '00000000-0000-0000-0000-000000000000')]
        public readonly string $patientId,

        #[OA\Property(description: 'ID de l’élément de prescription', type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999')]
        public readonly string $prescriptionItemId,

        #[OA\Property(description: 'ID de l’insuline injectée', type: 'string', format: 'uuid', example: '66bb1245-12f4-4b53-8811-7a6543210999')]
        public readonly string $insulinId,

        #[OA\Property(description: 'Date et heure de l’injection', type: 'string', format: 'date-time', example: '2026-08-10T08:00:00Z')]
        public readonly \DateTimeImmutable $injectedAt,

        #[OA\Property(description: 'Dose d’insuline en unités', type: 'string', example: '12.00')]
        public readonly string $doseUnits,

        #[OA\Property(description: 'Site d’injection', type: 'string', example: 'ABDOMEN', nullable: true)]
        public readonly ?string $injectionSite,

        #[OA\Property(description: 'Statut de l’injection', type: 'string', example: 'TAKEN', nullable: true)]
        public readonly ?string $status,

        #[OA\Property(description: 'ID de l’émetteur', type: 'string', format: 'uuid', example: '00000000-0000-0000-0000-000000000000', nullable: true)]
        public readonly ?string $issuerId,

        #[OA\Property(description: 'Notes complémentaires', type: 'string', example: 'Injection effectuée après le repas.', nullable: true)]
        public readonly ?string $notes,

        #[OA\Property(description: 'Date de création', type: 'string', format: 'date-time', example: '2026-08-10T10:30:00Z')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(description: 'Date de mise à jour', type: 'string', format: 'date-time', example: null, nullable: true)]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(InsulinInjection $injection): self
    {
        return new self(
            id: (string) $injection->getId(),
            patientId: (string) $injection->getPatient()?->getId(),
            prescriptionItemId: (string) $injection->getPrescriptionItem()?->getId(),
            insulinId: (string) $injection->getInsulin()?->getId(),
            injectedAt: $injection->getInjectedAt(),
            doseUnits: $injection->getDoseUnits(),
            injectionSite: $injection->getInjectionSite()?->value,
            status: $injection->getStatus()?->value,
            issuerId: $injection->getIssuer()?->getId(),
            notes: $injection->getNotes(),
            createdAt: $injection->getCreatedAt(),
            updatedAt: $injection->getUpdatedAt()
        );
    }
}