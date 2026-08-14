<?php

namespace App\DTO\Request\Identity;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'UserCreateRequestDTO',
    description: 'Données requises pour la création d’un compte utilisateur (Patient)'
)]
class UserCreateRequestDTO
{
    #[Assert\NotBlank(message: 'L’e-mail est obligatoire.')]
    #[Assert\Email(message: 'Format d’e-mail invalide.')]
    #[OA\Property(
        description: 'Adresse e-mail unique de l’utilisateur',
        type: 'string',
        format: 'email',
        example: 'patient@diabcare.com'
    )]
    public string $email;

    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire.')]
    #[Assert\Length(min: 8, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.')]
    #[OA\Property(
        description: 'Mot de passe (minimum 8 caractères)',
        type: 'string',
        format: 'password',
        example: 'SecurePassword123!'
    )]
    public string $password;

    #[Assert\NotBlank(message: 'Le nom complet est obligatoire.')]
    #[OA\Property(
        description: 'Nom complet de l’utilisateur',
        type: 'string',
        example: 'Jean Mukendi'
    )]
    public string $fullName;

    #[OA\Property(
        description: 'Numéro de téléphone',
        type: 'string',
        example: '+243990000000',
        nullable: true
    )]
    public ?string $phone = null;

    #[OA\Property(
        description: 'Genre de l’utilisateur (basé sur l’énumération Gender)',
        type: 'string',
        example: 'MALE',
        nullable: true,
        enum: ['MALE', 'FEMALE']
    )]
    public ?string $gender = null;

    #[OA\Property(
        description: 'Langue ou locale préférée',
        type: 'string',
        example: 'fr'
    )]
    public ?string $locale = 'fr';
}
