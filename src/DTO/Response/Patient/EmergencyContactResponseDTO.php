<?php

namespace App\DTO\Response\Patient;

use App\Entity\Patient\EmergencyContact;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'EmergencyContactResponseDTO',
    title: 'EmergencyContactResponseDTO',
    description: 'Structure de réponse pour un contact d’urgence'
)]
class EmergencyContactResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '55ee6677-8899-0011-2233-445566778899', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'ID du patient')]
        public readonly string $patientId,

        #[OA\Property(type: 'string', example: 'Marie Dupont', description: 'Nom complet')]
        public readonly string $fullName,

        #[OA\Property(type: 'string', example: 'Conjointe', description: 'Relation')]
        public readonly string $relationship,

        #[OA\Property(type: 'string', example: '+243900000000', description: 'Téléphone')]
        public readonly string $phone,

        #[OA\Property(type: 'string', format: 'email', nullable: true, example: 'marie@example.com', description: 'Email')]
        public readonly ?string $email,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T10:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(EmergencyContact $contact): self
    {
        return new self(
            id: (string) $contact->getId(),
            patientId: (string) $contact->getPatient()?->getId(),
            fullName: $contact->getFullName(),
            relationship: $contact->getRelationship(),
            phone: $contact->getPhone(),
            email: $contact->getEmail(),
            createdAt: $contact->getCreatedAt(),
            updatedAt: $contact->getUpdatedAt()
        );
    }
}
