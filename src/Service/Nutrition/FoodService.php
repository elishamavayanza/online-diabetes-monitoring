<?php

namespace App\Service\Nutrition;

use App\DTO\Feedback;
use App\DTO\Request\Nutrition\FoodRequestDTO;
use App\Mapper\Nutrition\FoodMapper;
use App\Repository\Identity\UserRepository;
use App\Repository\Nutrition\FoodCategoryRepository;
use App\Repository\Nutrition\FoodRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FoodService
{
    public function __construct(
        private readonly FoodRepository $repository,
        private readonly FoodCategoryRepository $categoryRepository,
        private readonly UserRepository $userRepository,
        private readonly FoodMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {
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
}
