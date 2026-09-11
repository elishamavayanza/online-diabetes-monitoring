<?php

declare(strict_types=1);

namespace App\Entity\System;

use App\Entity\Common\BaseEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Configuration globale de la plateforme (ligne unique sinleton).
 *
 * Contient l'identité visuelle (nom, logo) et les textes présentés
 * sur la page d'accueil publique, modifiables par le ROOT.
 */
#[ORM\Entity]
#[ORM\Table(name: 'system_settings')]
class SystemSettings extends BaseEntity
{
    #[ORM\Column(type: Types::STRING, length: 150)]
    private ?string $systemName = null;

    #[ORM\Column(type: Types::STRING, length: 500, nullable: true)]
    private ?string $logoUrl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $heroTitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $heroSubtitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $aboutTitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $aboutContent = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $featuresTitle = null;

    /**
     * @var list<array{title?: string|null, description?: string|null}>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $features = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $usersTitle = null;

    /**
     * @var list<array{title?: string|null, description?: string|null}>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $users = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ctaTitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ctaSubtitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $footerTagline = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $footerCopyright = null;

    public function getSystemName(): ?string
    {
        return $this->systemName;
    }

    public function setSystemName(?string $systemName): static
    {
        $this->systemName = $systemName;

        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(?string $logoUrl): static
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    public function getHeroTitle(): ?string
    {
        return $this->heroTitle;
    }

    public function setHeroTitle(?string $heroTitle): static
    {
        $this->heroTitle = $heroTitle;

        return $this;
    }

    public function getHeroSubtitle(): ?string
    {
        return $this->heroSubtitle;
    }

    public function setHeroSubtitle(?string $heroSubtitle): static
    {
        $this->heroSubtitle = $heroSubtitle;

        return $this;
    }

    public function getAboutTitle(): ?string
    {
        return $this->aboutTitle;
    }

    public function setAboutTitle(?string $aboutTitle): static
    {
        $this->aboutTitle = $aboutTitle;

        return $this;
    }

    public function getAboutContent(): ?string
    {
        return $this->aboutContent;
    }

    public function setAboutContent(?string $aboutContent): static
    {
        $this->aboutContent = $aboutContent;

        return $this;
    }

    public function getFeaturesTitle(): ?string
    {
        return $this->featuresTitle;
    }

    public function setFeaturesTitle(?string $featuresTitle): static
    {
        $this->featuresTitle = $featuresTitle;

        return $this;
    }

    /**
     * @return list<array{title?: string|null, description?: string|null}>|null
     */
    public function getFeatures(): ?array
    {
        return $this->features;
    }

    /**
     * @param list<array{title?: string|null, description?: string|null}>|null $features
     */
    public function setFeatures(?array $features): static
    {
        $this->features = $features;

        return $this;
    }

    public function getUsersTitle(): ?string
    {
        return $this->usersTitle;
    }

    public function setUsersTitle(?string $usersTitle): static
    {
        $this->usersTitle = $usersTitle;

        return $this;
    }

    /**
     * @return list<array{title?: string|null, description?: string|null}>|null
     */
    public function getUsers(): ?array
    {
        return $this->users;
    }

    /**
     * @param list<array{title?: string|null, description?: string|null}>|null $users
     */
    public function setUsers(?array $users): static
    {
        $this->users = $users;

        return $this;
    }

    public function getCtaTitle(): ?string
    {
        return $this->ctaTitle;
    }

    public function setCtaTitle(?string $ctaTitle): static
    {
        $this->ctaTitle = $ctaTitle;

        return $this;
    }

    public function getCtaSubtitle(): ?string
    {
        return $this->ctaSubtitle;
    }

    public function setCtaSubtitle(?string $ctaSubtitle): static
    {
        $this->ctaSubtitle = $ctaSubtitle;

        return $this;
    }

    public function getFooterTagline(): ?string
    {
        return $this->footerTagline;
    }

    public function setFooterTagline(?string $footerTagline): static
    {
        $this->footerTagline = $footerTagline;

        return $this;
    }

    public function getFooterCopyright(): ?string
    {
        return $this->footerCopyright;
    }

    public function setFooterCopyright(?string $footerCopyright): static
    {
        $this->footerCopyright = $footerCopyright;

        return $this;
    }
}