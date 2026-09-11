<?php

declare(strict_types=1);

namespace App\Service\System;

use App\DTO\Feedback;
use App\DTO\Request\System\SystemSettingsUpdateRequestDTO;
use App\Entity\System\SystemSettings;
use App\Mapper\System\SystemSettingsMapper;
use App\Security\SecurityServiceInterface;
use App\Service\File\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SystemSettingsService
{
    public const LOGO_SUBFOLDER = 'branding';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FileUploaderService $fileUploaderService,
        private readonly SystemSettingsMapper $mapper,
        private readonly SecurityServiceInterface $securityService
    ) {
    }

    /**
     * Récupère (et initialise au besoin) la configuration singleton.
     */
    public function getSingleton(): SystemSettings
    {
        $settings = $this->entityManager
            ->getRepository(SystemSettings::class)
            ->findOneBy([], ['id' => 'ASC']);

        if (!$settings) {
            $settings = new SystemSettings();
            $settings->setSystemName('OnlineDIAB');
            $this->entityManager->persist($settings);
            $this->entityManager->flush();
        }

        return $settings;
    }

    public function get(): Feedback
    {
        $feedback = new Feedback();

        try {
            $feedback->setData($this->mapper->mapEntityToResponse($this->getSingleton()))
                ->setFlushDescription('Configuration système récupérée avec succès.')
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(SystemSettingsUpdateRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            if (!$this->securityService->isSuperAdmin()) {
                throw new AccessDeniedException('Seul le super administrateur peut modifier la configuration système.');
            }

            $settings = $this->getSingleton();

            if ($dto->systemName !== null) {
                $settings->setSystemName(trim($dto->systemName));
            }

            if ($dto->logoFile) {
                if ($settings->getLogoUrl()) {
                    $this->fileUploaderService->remove(basename($settings->getLogoUrl()), self::LOGO_SUBFOLDER);
                }
                $fileName = $this->fileUploaderService->upload($dto->logoFile, self::LOGO_SUBFOLDER);
                $settings->setLogoUrl('/uploads/files/' . self::LOGO_SUBFOLDER . '/' . $fileName);
            }

            foreach ([
                'heroTitle' => $dto->heroTitle,
                'heroSubtitle' => $dto->heroSubtitle,
                'aboutTitle' => $dto->aboutTitle,
                'aboutContent' => $dto->aboutContent,
                'featuresTitle' => $dto->featuresTitle,
                'usersTitle' => $dto->usersTitle,
                'ctaTitle' => $dto->ctaTitle,
                'ctaSubtitle' => $dto->ctaSubtitle,
                'footerTagline' => $dto->footerTagline,
                'footerCopyright' => $dto->footerCopyright,
            ] as $method => $value) {
                if ($value !== null) {
                    $settings->{'set' . ucfirst($method)}($value);
                }
            }

            if (is_array($dto->features)) {
                $settings->setFeatures(array_values($dto->features));
            }

            if (is_array($dto->users)) {
                $settings->setUsers(array_values($dto->users));
            }

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($settings))
                ->setFlushDescription('Configuration système enregistrée avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}