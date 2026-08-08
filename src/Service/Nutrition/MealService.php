<?php

namespace App\Service\Nutrition;

use App\DTO\Feedback;
use App\DTO\Request\Nutrition\MealRequestDTO;
use App\Mapper\Nutrition\MealMapper;
use App\Repository\Nutrition\MealRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MealService
{
    public function __construct(
        private readonly MealRepository $repository,
        private readonly MealMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(MealRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_MEAL->value);

            $meal = $this->mapper->mapRequestToEntity($dto);

            $this->entityManager->persist($meal);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($meal))
                ->setFlushDescription("Repas créé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
