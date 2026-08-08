<?php

namespace App\Service\Nutrition;

use App\DTO\Feedback;
use App\DTO\Request\Nutrition\FoodCategoryRequestDTO;
use App\Mapper\Nutrition\FoodCategoryMapper;
use App\Repository\Nutrition\FoodCategoryRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FoodCategoryService
{
    public function __construct(
        private readonly FoodCategoryRepository $repository,
        private readonly FoodCategoryMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {
    }

    public function create(FoodCategoryRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkProfessionalAccess(
                SecurityAction::MANAGE_FOOD_CATEGORY
            );

            $category = $this->mapper->mapRequestToEntity($dto);

            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return $feedback
                ->setData(
                    $this->mapper->mapEntityToResponse($category)
                )
                ->setFlushDescription(
                    'Catégorie d’aliment créée avec succès.'
                )
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Accès refusé : ' . $e->getMessage()
                )
                ->autoInitFlush();

        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Erreur : ' . $e->getMessage()
                )
                ->autoInitFlush();
        }
    }
}
