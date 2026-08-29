<?php

namespace App\DTO\Response\Identity;

use App\Entity\Identity\User;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'UserResponseDTO',
    description: 'Structure de base des données renvoyées pour un utilisateur'
)]
class UserResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'string', example: '1', description: 'Identifiant unique')]
        public readonly string $id,

        #[OA\Property(type: 'string', format: 'email', example: 'user@diabcare.com', description: 'E-mail')]
        public readonly string $email,

        #[OA\Property(type: 'string', nullable: true, example: '+243990000000', description: 'Téléphone')]
        public readonly ?string $phone,

        #[OA\Property(type: 'string', example: 'Jean Mukendi', description: 'Nom complet')]
        public readonly string $fullName,

        #[OA\Property(type: 'string', format: 'uri', nullable: true, example: 'https://storage.diabcare.com/avatars/default.jpg', description: 'Avatar URL')]
        public readonly ?string $avatarUrl,

        #[OA\Property(type: 'string', nullable: true, example: 'MALE', description: 'Genre')]
        public readonly ?string $gender,

        #[OA\Property(type: 'string', example: 'fr', description: 'Locale')]
        public readonly string $locale,

        #[OA\Property(type: 'string', example: 'ACTIVE', description: 'Statut')]
        public readonly string $status,

        #[OA\Property(type: 'string', nullable: true, example: 'org-uuid-123', description: 'ID de l’organisation')]
        public readonly ?string $organizationId,

        #[OA\Property(type: 'string', nullable: true, example: 'Hôpital Général de Goma', description: 'Nom de l’organisation')]
        public readonly ?string $organizationName,

        #[OA\Property(ref: new Model(type: AddressResponseDTO::class), nullable: true, description: 'Adresse de l’utilisateur')]
        public readonly ?AddressResponseDTO $address,

        #[OA\Property(type: 'array', items: new OA\Items(type: 'string'), example: ['ROLE_USER'], description: 'Rôles')]
        public readonly array $roles,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-08-10T11:30:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de mise à jour')]
        public readonly ?\DateTimeImmutable $updatedAt
    ) {}

    public static function fromEntity(User $user): self
    {
        $orgId = null;
        $orgName = null;

        foreach ($user->getOrganizationMemberships() as $membership) {
            if ($membership->getStatus()?->isActive() && $membership->getOrganization() !== null) {
                $orgId = (string) $membership->getOrganization()->getId();
                $orgName = $membership->getOrganization()->getName(); // Assurez-vous que getName() existe sur HealthcareOrganization
                break;
            }
        }

        return new self(
            id: (string) $user->getId(),
            email: $user->getEmail(),
            phone: $user->getPhone(),
            fullName: $user->getFullName(),
            avatarUrl: $user->getAvatarUrl(),
            gender: $user->getGender()?->value,
            locale: $user->getLocale(),
            status: $user->getStatus()?->value ?? '',
            organizationId: $orgId,
            organizationName: $orgName,
            address: $user->getAddress() ? AddressResponseDTO::fromEntity($user->getAddress()) : null,
            roles: $user->getRoles(),
            createdAt: $user->getCreatedAt(),
            updatedAt: $user->getUpdatedAt()
        );
    }
}
