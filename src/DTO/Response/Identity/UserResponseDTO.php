<?php

namespace App\DTO\Response\Identity;

use App\Entity\Identity\User;

class UserResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly ?string $avatarUrl,
        public readonly ?string $gender,
        public readonly string $locale,
        public readonly string $status,
        public readonly ?AddressResponseDTO $address,
        public readonly array $roles,
        public readonly \DateTimeImmutable $createdAt,
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(User $user): self
    {
        return new self(
            id: (string) $user->getId(),
            email: $user->getEmail(),
            phone: $user->getPhone(),
            firstName: $user->getFirstName(),
            lastName: $user->getLastName(),
            avatarUrl: $user->getAvatarUrl(),
            gender: $user->getGender()?->value,
            locale: $user->getLocale(),
            status: $user->getStatus()?->value ?? '',
            address: AddressResponseDTO::fromEntity($user->getAddress()),
            roles: $user->getRoles(),
            createdAt: $user->getCreatedAt(),
            updatedAt: $user->getUpdatedAt()
        );
    }
}
