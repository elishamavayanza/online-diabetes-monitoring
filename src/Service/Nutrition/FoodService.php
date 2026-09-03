<?php

namespace App\Service\Nutrition;

use App\DTO\Feedback;
use App\DTO\Request\Nutrition\FoodRequestDTO;
use App\Entity\Nutrition\Food;
use App\Mapper\Nutrition\FoodMapper;
use App\Repository\Identity\UserRepository;
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
        private readonly UserRepository $userRepository,
        private readonly FoodMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService,
        private readonly FileUploaderService $fileUploaderService
    ) {
    }

    public function all(): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkProfessionalAccess(
                SecurityAction::MANAGE_FOOD
            );

            $foods = $this->repository->findAll();
            $data = array_map(fn(Food $food) => $this->mapper->mapEntityToResponse($food), $foods);

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
            $this->securityService->checkProfessionalAccess(
                SecurityAction::MANAGE_FOOD
            );

            $food = $this->repository->find($id);

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

            $createdBy = null;

            if ($dto->createdById) {
                $createdBy = $this->userRepository->find(
                    $dto->createdById
                );
            }

            $food = $this->mapper->mapRequestToEntity(
                $dto,
                $category,
                $createdBy
            );

            $this->entityManager->persist($food);
            $this->entityManager->flush();

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

            $food = $this->repository->find($id);

            if (!$food) {
                return $feedback
                    ->setErrorFlushDescription("Aliment introuvable.")
                    ->autoInitFlush();
            }

            $category = $this->categoryRepository->find(
                $dto->categoryId
            );

            if (!$category) {
                return $feedback
                    ->setErrorFlushDescription("Catégorie d'aliment introuvable.")
                    ->autoInitFlush();
            }

            $createdBy = null;

            if ($dto->createdById) {
                $createdBy = $this->userRepository->find(
                    $dto->createdById
                );
            }

            $food = $this->mapper->mapRequestToEntity(
                $dto,
                $category,
                $createdBy,
                $food
            );

            $this->entityManager->flush();

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

            $food = $this->repository->find($id);

            if (!$food) {
                return $feedback
                    ->setErrorFlushDescription("Aliment introuvable.")
                    ->autoInitFlush();
            }

            $this->entityManager->remove($food);
            $this->entityManager->flush();

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
