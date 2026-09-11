<?php

namespace App\DTO\Request\Identity;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'UserUpdateRequestDTO',
    description: 'Données de mise à jour d’un compte utilisateur (tous les champs sont optionnels)'
)]
class UserUpdateRequestDTO
{
    #[Assert\Email(message: 'Format d’e-mail invalide.')]
    #[OA\Property(
        description: 'Adresse e-mail unique de l’utilisateur',
        type: 'string',
        format: 'email',
        example: 'admin@diabcare.com'
    )]
    public ?string $email = null;

    #[Assert\Length(min: 8, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.')]
    #[OA\Property(
        description: 'Nouveau mot de passe (minimum 8 caractères)',
        type: 'string',
        format: 'password',
        example: 'SecurePassword123!'
    )]
    public ?string $password = null;

    #[OA\Property(
        description: 'Nom complet de l’utilisateur',
        type: 'string',
        example: 'Jean Mukendi'
    )]
    public ?string $fullName = null;

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

    #[Assert\Image(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp']
    )]
    #[OA\Property(description: 'Nouvelle photo de profil (avatar)', type: 'string', format: 'binary', nullable: true)]
    public ?UploadedFile $avatarFile = null;

    #[OA\Property(
        description: 'URL ou chemin de la photo de profil existante',
        type: 'string',
        nullable: true
    )]
    public ?string $avatarUrl = null;
}