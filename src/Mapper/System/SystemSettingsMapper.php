<?php

declare(strict_types=1);

namespace App\Mapper\System;

use App\DTO\Response\System\SystemSettingsResponseDTO;
use App\Entity\System\SystemSettings;

class SystemSettingsMapper
{
    public function mapEntityToResponse(SystemSettings $settings): SystemSettingsResponseDTO
    {
        $dto = new SystemSettingsResponseDTO();
        $dto->id = $settings->getId();
        $dto->systemName = $settings->getSystemName();
        $dto->logoUrl = $settings->getLogoUrl();
        $dto->heroTitle = $settings->getHeroTitle();
        $dto->heroSubtitle = $settings->getHeroSubtitle();
        $dto->aboutTitle = $settings->getAboutTitle();
        $dto->aboutContent = $settings->getAboutContent();
        $dto->featuresTitle = $settings->getFeaturesTitle();
        $dto->features = $settings->getFeatures();
        $dto->usersTitle = $settings->getUsersTitle();
        $dto->users = $settings->getUsers();
        $dto->ctaTitle = $settings->getCtaTitle();
        $dto->ctaSubtitle = $settings->getCtaSubtitle();
        $dto->footerTagline = $settings->getFooterTagline();
        $dto->footerCopyright = $settings->getFooterCopyright();

        return $dto;
    }
}