<?php

declare(strict_types=1);

namespace App\DTO\Response\System;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'SystemSettingsResponseDTO',
    description: 'Configuration système renvoyée par l’API.'
)]
class SystemSettingsResponseDTO
{
    #[OA\Property(description: 'Identifiant unique de la configuration', type: 'integer', example: 1)]
    public ?string $id = null;

    #[OA\Property(description: 'Nom affiché du système', type: 'string', example: 'OnlineDIAB')]
    public ?string $systemName = null;

    #[OA\Property(description: 'URL publique du logo du système', type: 'string', nullable: true)]
    public ?string $logoUrl = null;

    #[OA\Property(description: 'Titre de la section héro', type: 'string', nullable: true)]
    public ?string $heroTitle = null;

    #[OA\Property(description: 'Sous-titre de la section héro', type: 'string', nullable: true)]
    public ?string $heroSubtitle = null;

    #[OA\Property(description: 'Titre de la section « À propos »', type: 'string', nullable: true)]
    public ?string $aboutTitle = null;

    #[OA\Property(description: 'Contenu de la section « À propos »', type: 'string', nullable: true)]
    public ?string $aboutContent = null;

    #[OA\Property(description: 'Titre de la section « Fonctionnalités »', type: 'string', nullable: true)]
    public ?string $featuresTitle = null;

    /** @var list<array{title?: string|null, description?: string|null}>|null */
    public ?array $features = null;

    #[OA\Property(description: 'Titre de la section « Pour qui ? »', type: 'string', nullable: true)]
    public ?string $usersTitle = null;

    /** @var list<array{title?: string|null, description?: string|null}>|null */
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