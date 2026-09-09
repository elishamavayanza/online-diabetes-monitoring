<?php

namespace App\DTO\Request\Security;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Payload d'une suspension (organisation ou compte).
 *
 * Le délai est exprimé soit par une durée en jours (durationDays), soit par une
 * date de fin (endsAt). Si les deux sont absents, la suspension est indéterminée
 * (levée uniquement manuellement).
 */
class SuspensionRequestDTO
{
    #[Assert\NotBlank(message: 'Le motif de la suspension est obligatoire.')]
    #[Assert\Length(
        max: 500,
        maxMessage: 'Le motif ne peut pas dépasser {{ limit }} caractères.'
    )]
    public ?string $reason = null;

    #[Assert\Type('string')]
    public ?string $startsAt = null;

    #[Assert\Type('int')]
    #[Assert\Positive(message: 'La durée doit être positive.')]
    public ?int $durationDays = null;

    #[Assert\Type('string')]
    public ?string $endsAt = null;
}