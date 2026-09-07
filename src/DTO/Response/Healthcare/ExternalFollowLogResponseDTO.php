<?php

namespace App\DTO\Response\Healthcare;

use App\Entity\Healthcare\ExternalFollowLog;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ExternalFollowLogResponseDTO',
    title: 'ExternalFollowLogResponseDTO',
    description: 'Entrée du journal des actions d’un professionnel externe sur le dossier d’un patient'
)]
class ExternalFollowLogResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'integer', format: 'int64', example: 51, description: 'Identifiant de l’entrée de journal')]
        public readonly string $id,

        #[OA\Property(type: 'integer', format: 'int64', example: 6, description: 'Identifiant du patient')]
        public readonly string $patientId,

        #[OA\Property(type: 'string', example: 'Dr Jean DUPONT', description: 'Professionnel externe concerné')]
        public readonly string $professionalName,

        #[OA\Property(type: 'string', example: 'record_glucose', description: 'Action (SecurityAction)')]
        public readonly string $action,

        #[OA\Property(type: 'string', example: 'Saisie d’une mesure de glycémie', description: 'Libellé lisible de l’action')]
        public readonly ?string $actionLabel,

        #[OA\Property(type: 'string', nullable: true, example: null, description: 'Détail éventuel')]
        public readonly ?string $detail,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-09-07T10:15:00Z', description: 'Date de l’action')]
        public readonly \DateTimeImmutable $createdAt
    ) {}

    public static function fromEntity(ExternalFollowLog $log): self
    {
        return new self(
            id: (string) $log->getId(),
            patientId: (string) $log->getPatient()?->getId(),
            professionalName: $log->getProfessional()?->getFullName() ?? '',
            action: (string) $log->getAction(),
            actionLabel: $log->getActionLabel(),
            detail: $log->getDetail(),
            createdAt: $log->getCreatedAt()
        );
    }
}