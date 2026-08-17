<?php

namespace App\Service\Nutrition;

use App\DTO\Feedback;
use App\DTO\Request\Nutrition\MealItemRequestDTO;
use App\Mapper\Nutrition\MealItemMapper;
use App\Repository\Identity\PatientRepository;
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
        private readonly PatientRepository $patientRepository,
        private readonly MealItemMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function get(int $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $mealItem = $this->repository->find($id);
            if (!$mealItem) {
                return $feedback->setErrorFlushDescription("Élément de repas introuvable.")->autoInitFlush();
            }

            // Vérification d'accès : le patient lui-même ou son médecin assigné
            $this->securityService->checkPatientAccess($mealItem->getMeal()->getPatient(), SecurityAction::MANAGE_MEAL);

            $feedback->setData($this->mapper->mapEntityToResponse($mealItem))
                ->setFlushDescription("Élément récupéré avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    /**
     * Récupère tous les éléments de repas pour un patient donné (accès médecin)
     */
    public function getByPatient(int $patientId): Feedback
    {
        $feedback = new Feedback();

        try {
            // 1. Récupérer l'entité Patient via son repository
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            // 2. CORRECTION : Vérifier l'accès au patient spécifique avec une action de lecture autorisée pour le clinicien
            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_NUTRITION);

            // 3. Récupération des éléments avec votre repository
            $mealItems = $this->repository->findByPatient($patientId);

            // 4. Transformation en DTO
            $data = array_map(fn($item) => $this->mapper->mapEntityToResponse($item), $mealItems);

            $feedback->setData($data)
                ->setFlushDescription("Éléments du patient récupérés.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé au dossier de ce patient : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function create(MealItemRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $meal = $this->mealRepository->find($dto->mealId);
            if (!$meal) {
                return $feedback->setErrorFlushDescription("Repas introuvable.")->autoInitFlush();
            }

            // Vérifie que l'utilisateur a le droit de gérer ce repas (propriétaire ou professionnel autorisé)
            $this->securityService->checkPatientAccess($meal->getPatient(), SecurityAction::MANAGE_MEAL);

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

    public function update(int $id, MealItemRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $mealItem = $this->repository->find($id);
            if (!$mealItem) {
                return $feedback->setErrorFlushDescription("Élément de repas introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($mealItem->getMeal()->getPatient(), SecurityAction::MANAGE_MEAL);

            $meal = $this->mealRepository->find($dto->mealId);
            if (!$meal) {
                return $feedback->setErrorFlushDescription("Repas introuvable.")->autoInitFlush();
            }

            $food = $this->foodRepository->find($dto->foodId);
            if (!$food) {
                return $feedback->setErrorFlushDescription("Aliment introuvable.")->autoInitFlush();
            }

            // Utilisation du mapper avec l'entité existante pour la mise à jour
            $mealItem = $this->mapper->mapRequestToEntity($dto, $meal, $food, $mealItem);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($mealItem))
                ->setFlushDescription("Élément de repas mis à jour avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function delete(int $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $mealItem = $this->repository->find($id);
            if (!$mealItem) {
                return $feedback->setErrorFlushDescription("Élément de repas introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($mealItem->getMeal()->getPatient(), SecurityAction::MANAGE_MEAL);

            $this->entityManager->remove($mealItem);
            $this->entityManager->flush();

            $feedback->setFlushDescription("Élément de repas supprimé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
