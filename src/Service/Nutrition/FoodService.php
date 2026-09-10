<?php

namespace App\Service\Nutrition;

use App\Cache\CatalogCache;
use App\DTO\Feedback;
use App\DTO\Request\Nutrition\FoodRequestDTO;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Nutrition\Food;
use App\Mapper\Nutrition\FoodMapper;
use App\Repository\Nutrition\FoodCategoryRepository;
use App\Repository\Nutrition\FoodRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use App\Service\File\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FoodService
{
    public function __construct(
        private readonly FoodRepository $repository,
        private readonly FoodCategoryRepository $categoryRepository,
        private readonly FoodMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService,
        private readonly FileUploaderService $fileUploaderService,
        private readonly CatalogCache $catalogCache
    ) {
    }

    private function currentOrganization(): ?HealthcareOrganization
    {
        if ($this->securityService->isSuperAdmin()) {
            return null;
        }

        $user = $this->securityService->getCurrentUser();
        foreach ($user->getOrganizationMemberships() as $membership) {
            if ($membership->getStatus()?->isActive() && $membership->getOrganization() !== null) {
                return $membership->getOrganization();
            }
        }

        throw new AccessDeniedException('Aucune organisation active n’est associée à cet utilisateur.');
    }

    private function findAccessibleFood(int $id): ?Food
    {
        $organization = $this->currentOrganization();

        return $organization === null
            ? $this->repository->find($id)
            : $this->repository->findOneByIdAndOrganization($id, $organization);
    }

    private function assertCurrentProfessionalIsCreator(Food $food): void
    {
        if ($this->securityService->isSuperAdmin()) {
            return;
        }

        $user = $this->securityService->getCurrentUser();
        if (!$user instanceof HealthcareProfessional || $food->getCreatedBy()?->getId() !== $user->getId()) {
            throw new AccessDeniedException('Seul le nutritionniste ayant créé cet aliment peut le modifier ou le supprimer.');
        }
    }

    private function cacheKey(?HealthcareOrganization $organization): string
    {
        return 'foods.' . ($organization?->getId() ?? 'root');
    }

    public function all(): Feedback
    {
        $feedback = new Feedback();

        try {
            if (!$this->securityService->hasAnyRole(['ROLE_CLINICIAN', 'ROLE_NUTRITIONIST', 'ROLE_ADMIN', 'ROLE_ROOT', 'ROLE_PATIENT'])) {
                throw new AccessDeniedException("Accès non autorisé.");
            }

            $organization = $this->currentOrganization();

            $data = $this->catalogCache->get($this->cacheKey($organization), function () use ($organization) {
                $foods = $organization === null
                    ? $this->repository->findAllWithCategoryAndCreator()
                    : $this->repository->findByOrganizationWithCategoryAndCreator($organization);

                return array_map(fn(Food $food) => $this->mapper->mapEntityToResponse($food), $foods);
            });

            return $feedback
                ->setData($data)
                ->setFlushDescription("Liste des aliments récupérée avec succès.")
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription("Accès refusé : " . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription("Erreur : " . $e->getMessage())
                ->autoInitFlush();
        }
    }

    public function getById(int $id): Feedback
    {
        $feedback = new Feedback();

        try {
            if (!$this->securityService->hasAnyRole(['ROLE_CLINICIAN', 'ROLE_NUTRITIONIST', 'ROLE_ADMIN', 'ROLE_ROOT', 'ROLE_PATIENT'])) {
                throw new AccessDeniedException("Accès non autorisé.");
            }

            $food = $this->findAccessibleFood($id);

            if (!$food) {
                return $feedback
                    ->setErrorFlushDescription("Aliment introuvable.")
                    ->autoInitFlush();
            }

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($food))
                ->setFlushDescription("Aliment récupéré avec succès.")
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription("Accès refusé : " . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription("Erreur : " . $e->getMessage())
                ->autoInitFlush();
        }
    }

    public function create(FoodRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkProfessionalAccess(
                SecurityAction::MANAGE_FOOD
            );

            $category = $this->categoryRepository->find(
                $dto->categoryId
            );

            if (!$category) {
                return $feedback
                    ->setErrorFlushDescription(
                        "Catégorie d'aliment introuvable."
                    )
                    ->autoInitFlush();
            }

            $createdBy = $this->securityService->getCurrentUser();
            if (!$createdBy instanceof HealthcareProfessional) {
                throw new AccessDeniedException('Seul un professionnel peut créer un aliment.');
            }

            $food = $this->mapper->mapRequestToEntity(
                $dto,
                $category,
                $createdBy
            );
            $organization = $this->currentOrganization();
            $food->setOrganization($organization);

            $this->entityManager->persist($food);
            $this->entityManager->flush();
            $this->catalogCache->evict($this->cacheKey($organization));

            return $feedback
                ->setData(
                    $this->mapper->mapEntityToResponse($food)
                )
                ->setFlushDescription(
                    "Aliment créé avec succès."
                )
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription(
                    "Accès refusé : " . $e->getMessage()
                )
                ->autoInitFlush();

        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription(
                    "Erreur : " . $e->getMessage()
                )
                ->autoInitFlush();
        }
    }

    public function update(int $id, FoodRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkProfessionalAccess(
                SecurityAction::MANAGE_FOOD
            );

            $food = $this->findAccessibleFood($id);

            if (!$food) {
                return $feedback
                    ->setErrorFlushDescription("Aliment introuvable.")
                    ->autoInitFlush();
            }

            $this->assertCurrentProfessionalIsCreator($food);

            $category = $this->categoryRepository->find(
                $dto->categoryId
            );

            if (!$category) {
                return $feedback
                    ->setErrorFlushDescription("Catégorie d'aliment introuvable.")
                    ->autoInitFlush();
            }

            $food = $this->mapper->mapRequestToEntity(
                $dto,
                $category,
                $food->getCreatedBy(),
                $food
            );

            $this->entityManager->flush();
            $this->catalogCache->evict($this->cacheKey($food->getOrganization()));

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($food))
                ->setFlushDescription("Aliment mis à jour avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription("Accès refusé : " . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription("Erreur : " . $e->getMessage())
                ->autoInitFlush();
        }
    }

    public function delete(int $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkProfessionalAccess(
                SecurityAction::MANAGE_FOOD
            );

            $food = $this->findAccessibleFood($id);

            if (!$food) {
                return $feedback
                    ->setErrorFlushDescription("Aliment introuvable.")
                    ->autoInitFlush();
            }

            $this->assertCurrentProfessionalIsCreator($food);

            $this->entityManager->remove($food);
            $this->entityManager->flush();
            $this->catalogCache->evict($this->cacheKey($food->getOrganization()));

            return $feedback
                ->setFlushDescription("Aliment supprimé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription("Accès refusé : " . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription("Erreur : " . $e->getMessage())
                ->autoInitFlush();
        }
    }

    public function uploadPhoto(UploadedFile $file, Request $request): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkProfessionalAccess(
                SecurityAction::MANAGE_FOOD
            );

            if (!str_starts_with((string) $file->getMimeType(), 'image/')) {
                return $feedback
                    ->setErrorFlushDescription('Le fichier doit être une image.')
                    ->autoInitFlush();
            }

            $fileName = $this->fileUploaderService->upload($file, 'foods');
            $relativePath = '/uploads/files/foods/' . $fileName;
            $absoluteUrl = rtrim($request->getSchemeAndHttpHost(), '/') . $relativePath;

            return $feedback
                ->setData(['url' => $absoluteUrl])
                ->setFlushDescription('Photo téléversée avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }
}
