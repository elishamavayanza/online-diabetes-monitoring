<?php

namespace App\DTO\Response\Healthcare;

use App\Entity\Healthcare\OrganizationMembership;

class OrganizationMembershipResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly string $organizationId,
        public readonly ?string $facilityId,
        public readonly ?string $departmentId,
        public readonly \DateTimeInterface $startDate,
        public readonly ?\DateTimeInterface $endDate,
        public readonly ?string $status,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(OrganizationMembership $membership): self
    {
        return new self(
            id: (string) $membership->getId(),
            userId: (string) $membership->getUser()?->getId(),
            organizationId: (string) $membership->getOrganization()?->getId(),
            facilityId: $membership->getFacility()?->getId() ? (string) $membership->getFacility()->getId() : null,
            departmentId: $membership->getDepartment()?->getId() ? (string) $membership->getDepartment()->getId() : null,
            startDate: $membership->getStartDate(),
            endDate: $membership->getEndDate(),
            status: $membership->getStatus()?->value,
            createdAt: $membership->getCreatedAt(),
            updatedAt: $membership->getUpdatedAt()
        );
    }
}
