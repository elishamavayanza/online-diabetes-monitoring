<?php

namespace App\DTO\Request\Notification;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'ReminderRuleRequestDTO',
    description: 'Structure de requête pour la création d’une règle de rappel'
)]
class ReminderRuleRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(type: 'string', format: 'uuid', example: '33bb1245-12f4-4b53-8811-7a6543210999', description: 'Identifiant du patient')]
        public readonly string $patientId,

        #[Assert\NotBlank]
        #[OA\Property(type: 'string', example: 'BLOOD_GLUCOSE', description: 'Type de cible')]
        public readonly mixed $targetType,

        #[Assert\Uuid]
        #[OA\Property(type: 'string', format: 'uuid', nullable: true, example: '11aa2233-4455-6677-8899-aabbccddeeff', description: 'ID de l’entité liée')]
        public readonly ?string $relatedEntityId,

        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        #[OA\Property(type: 'string', maxLength: 100, example: '0 8 * * *', description: 'Expression Cron')]
        public readonly string $cronExpression,

        #[Assert\NotNull]
        #[OA\Property(type: 'boolean', example: true, description: 'Règle active ou non')]
        public readonly bool $active
    ) {}
}
