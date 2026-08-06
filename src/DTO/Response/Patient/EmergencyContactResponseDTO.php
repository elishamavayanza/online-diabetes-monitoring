<?php

namespace App\DTO\Response\Patient;

use App\Entity\Patient\EmergencyContact;

class EmergencyContactResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $patientId,
        public readonly string $fullName,
        public readonly string $relationship,
        public readonly string $phone,
        public readonly ?string $email,
        public readonly \DateTimeImmutable $createdAt,
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
