<?php

namespace App\Service\Nutrition;

use App\DTO\Feedback;
use App\DTO\Request\Nutrition\MealItemRequestDTO;
use App\Mapper\Nutrition\MealItemMapper;
use App\Repository\Nutrition\MealItemRepository;
use App\Repository\Nutrition\MealRepository;
use App\Repository\Nutrition\FoodRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MealItemService
{
    public function __construct(
        private readonly MealItemRepository $repository,
        private readonly MealRepository $mealRepository,
        private readonly FoodRepository $foodRepository,
        private readonly MealItemMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(MealItemRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_MEAL->value);

            $meal = $this->mealRepository->find($dto->mealId);
            if (!$meal) {
                return $feedback->setErrorFlushDescription("Repas introuvable.")->autoInitFlush();
            }

            $food = $this->foodRepository->find($dto->foodId);
            if (!$food) {
                return $feedback->setErrorFlushDescription("Aliment introuvable.")->autoInitFlush();
            }

            $mealItem = $this->mapper->mapRequestToEntity($dto, $meal, $food);

            $this->entityManager->persist($mealItem);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($mealItem))
                ->setFlushDescription("Élément de repas ajouté avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
