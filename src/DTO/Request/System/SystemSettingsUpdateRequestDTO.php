<?php

declare(strict_types=1);

namespace App\DTO\Request\System;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'SystemSettingsUpdateRequestDTO',
    description: 'Configuration système modifiable par le ROOT (tous les champs sont optionnels, sauf le nom).'
)]
class SystemSettingsUpdateRequestDTO
{
    #[Assert\NotBlank(message: 'Le nom du système est obligatoire.')]
    #[Assert\Length(max: 150, maxMessage: 'Le nom du système ne peut pas dépasser {{ limit }} caractères.')]
    #[OA\Property(description: 'Nom affiché du système', type: 'string', example: 'OnlineDIAB')]
    public ?string $systemName = null;

    #[Assert\Image(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp']
    )]
    #[OA\Property(description: 'Nouveau logo du système', type: 'string', format: 'binary', nullable: true)]
    public ?UploadedFile $logoFile = null;

    #[OA\Property(description: 'Titre de la section héro de la page d’accueil', type: 'string', nullable: true)]
    public ?string $heroTitle = null;

    #[OA\Property(description: 'Sous-titre de la section héro', type: 'string', nullable: true)]
    public ?string $heroSubtitle = null;

    #[OA\Property(description: 'Titre de la section « À propos »', type: 'string', nullable: true)]
    public ?string $aboutTitle = null;

    #[OA\Property(description: 'Contenu de la section « À propos »', type: 'string', nullable: true)]
    public ?string $aboutContent = null;

    #[OA\Property(description: 'Titre de la section « Fonctionnalités »', type: 'string', nullable: true)]
    public ?string $featuresTitle = null;

    /**
     * @var list<array{title?: string|null, description?: string|null}>|null
     */
    #[OA\Property(
        description: 'Liste des fonctionnalités présentées (titre + description)',
        type: 'array',
        nullable: true,
        items: new OA\Items(
            properties: [
                new OA\Property(property: 'title', type: 'string', nullable: true),
                new OA\Property(property: 'description', type: 'string', nullable: true),
            ]
        )
    )]
    public ?array $features = null;

    #[OA\Property(description: 'Titre de la section « Pour qui ? »', type: 'string', nullable: true)]
    public ?string $usersTitle = null;

    /**
     * @var list<array{title?: string|null, description?: string|null}>|null
     */
    #[OA\Property(
        description: 'Liste des publics cibles (titre + description)',
        type: 'array',
        nullable: true,
        items: new OA\Items(
            properties: [
                new OA\Property(property: 'title', type: 'string', nullable: true),
                new OA\Property(property: 'description', type: 'string', nullable: true),
            ]
        )
    )]
    public ?array $users = null;

    #[OA\Property(description: 'Titre de l’appel à l’action final', type: 'string', nullable: true)]
    public ?string $ctaTitle = null;

    #[OA\Property(description: 'Sous-titre de l’appel à l’action final', type: 'string', nullable: true)]
    public ?string $ctaSubtitle = null;

    #[OA\Property(description: 'Slogan affiché dans le pied de page', type: 'string', nullable: true)]
    public ?string $footerTagline = null;

    #[OA\Property(description: 'Copyright affiché dans le pied de page', type: 'string', nullable: true)]
    public ?string $footerCopyright = null;
}