<?php

namespace App\DTO\Response\Healthcare;

use App\Entity\Healthcare\ExternalFollowInvitation;
use App\DTO\Response\Identity\AvatarUrl;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ExternalFollowInvitationResponseDTO',
    title: 'ExternalFollowInvitationResponseDTO',
    description: 'Invitation à suivre un patient émise vers un professionnel d’une autre organisation'
)]
class ExternalFollowInvitationResponseDTO
{
    public function __construct(
        #[OA\Property(type: 'integer', format: 'int64', example: 31, description: 'Identifiant de l’invitation')]
        public readonly string $id,

        #[OA\Property(type: 'integer', format: 'int64', example: 6, description: 'Identifiant du patient')]
        public readonly string $patientId,

        #[OA\Property(type: 'string', example: 'Marie KALALA', description: 'Nom du patient')]
        public readonly string $patientName,

        #[OA\Property(type: 'string', nullable: true, example: '/uploads/files/avatars/abc.jpg', description: 'Photo de profil du patient')]
        public readonly ?string $patientPhotoUrl,

        #[OA\Property(type: 'string', format: 'email', example: 'dr.dupont@centre2.com', description: 'Email du professionnel invité')]
        public readonly string $email,

        #[OA\Property(type: 'integer', format: 'int64', example: 14, description: 'Identifiant du professionnel invité')]
        public readonly string $professionalId,

        #[OA\Property(type: 'string', example: 'Dr Jean DUPONT', description: 'Nom du professionnel invité')]
        public readonly string $professionalName,

        #[OA\Property(type: 'integer', format: 'int64', example: 2, description: 'Identifiant de l’organisation émettrice')]
        public readonly string $organizationId,

        #[OA\Property(type: 'string', example: 'Centre DiabCare Kinshasa', description: 'Nom de l’organisation émettrice')]
        public readonly string $organizationName,

        #[OA\Property(type: 'string', enum: ['PENDING', 'ACCEPTED', 'DECLINED', 'REVOKED', 'CLOSED_BY_PROFESSIONAL', 'EXPIRED'], example: 'PENDING', description: 'Statut de l’invitation')]
        public readonly string $status,

        #[OA\Property(type: 'string', format: 'date', example: '2026-09-07', description: 'Début du délai d’accès')]
        public readonly ?\DateTimeInterface $startDate,

        #[OA\Property(type: 'string', format: 'date', example: '2026-12-06', description: 'Fin du délai d’accès')]
        public readonly ?\DateTimeInterface $endDate,

        #[OA\Property(type: 'string', nullable: true, example: 'Bonjour, merci de suivre ce patient.', description: 'Message d’accompagnement')]
        public readonly ?string $message,

        #[OA\Property(type: 'string', example: 'Sylvie MBALA', description: 'Émetteur de l’invitation')]
        public readonly string $invitedByName,

        #[OA\Property(type: 'string', format: 'date-time', example: '2026-09-07T09:00:00Z', description: 'Date de création')]
        public readonly \DateTimeImmutable $createdAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date d’acceptation')]
        public readonly ?\DateTimeImmutable $acceptedAt,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de coupure de l’accès')]
        public readonly ?\DateTimeImmutable $revokedAt,

        #[OA\Property(type: 'string', nullable: true, example: 'Le patient est pris en charge localement.', description: 'Motif de fermeture du suivi par le professionnel')]
        public readonly ?string $closureReason,

        #[OA\Property(type: 'string', format: 'date-time', nullable: true, example: null, description: 'Date de fermeture du suivi par le professionnel')]
        public readonly ?\DateTimeImmutable $closedByProfessionalAt
    ) {}

    public static function fromEntity(ExternalFollowInvitation $invitation): self
    {
        return new self(
            id: (string) $invitation->getId(),
            patientId: (string) $invitation->getPatient()?->getId(),
            patientName: $invitation->getPatient()?->getFullName() ?? '',
            patientPhotoUrl: AvatarUrl::toPublicUrl($invitation->getPatient()?->getAvatarUrl()),
            email: (string) $invitation->getEmail(),
            professionalId: (string) $invitation->getProfessional()?->getId(),
            professionalName: $invitation->getProfessional()?->getFullName() ?? '',
            organizationId: (string) $invitation->getOrganization()?->getId(),
            organizationName: $invitation->getOrganization()?->getName() ?? '',
            status: $invitation->getStatus()?->value ?? '',
            startDate: $invitation->getStartDate(),
            endDate: $invitation->getEndDate(),
            message: $invitation->getMessage(),
            invitedByName: $invitation->getInvitedBy()?->getFullName() ?? '',
            createdAt: $invitation->getCreatedAt(),
            acceptedAt: $invitation->getAcceptedAt(),
            revokedAt: $invitation->getRevokedAt(),
            closureReason: $invitation->getClosureReason(),
            closedByProfessionalAt: $invitation->getClosedByProfessionalAt()
        );
    }
}