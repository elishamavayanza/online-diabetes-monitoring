<?php

namespace App\Service\Nutrition;

use App\DTO\Feedback;
use App\DTO\Request\Nutrition\MealRequestDTO;
use App\Entity\Identity\Patient;
use App\Entity\Nutrition\Meal;
use App\Mapper\Nutrition\MealMapper;
use App\Repository\Identity\PatientRepository;
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
        private readonly SecurityServiceInterface $securityService,
        private readonly PatientRepository $patientRepository
    ) {}

    public function create(MealRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $currentUser = $this->securityService->getCurrentUser();

            // Si c'est un patient, le patientId du DTO est ignoré/forcé sur lui-même.
            // Si c'est un professionnel, le patientId devient obligatoire via le DTO.
            $patient = $this->resolvePatientForUser($currentUser, $dto->patientId);

            // Vérification de la permission de gérer les repas pour ce patient
            $this->securityService->checkPatientAccess($patient, SecurityAction::MANAGE_MEAL);

            $meal = $this->mapper->mapRequestToEntity($dto);
            $meal->setPatient($patient);

            if (method_exists($meal, 'setIssuer')) {
                $meal->setIssuer($currentUser);
            }

            $this->entityManager->persist($meal);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($meal))
                ->setFlushDescription("Repas créé avec succès.")
                ->autoInitFlush();

        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function list(?int $patientId): Feedback
    {
        $feedback = new Feedback();

        try {
            $currentUser = $this->securityService->getCurrentUser();
            $patient = $this->resolvePatientForUser($currentUser, $patientId);

            // Vérification des droits de lecture
            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_NUTRITION);

            $meals = $this->repository->findBy(['patient' => $patient]);
            $responseDTOs = array_map([$this->mapper, 'mapEntityToResponse'], $meals);

            $feedback->setData($responseDTOs)
                ->setFlushDescription("Liste des repas récupérée avec succès.")
                ->autoInitFlush();

        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function show(int $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $meal = $this->repository->find($id);
            if (!$meal) {
                throw new \InvalidArgumentException("Repas introuvable.");
            }

            $this->securityService->checkPatientAccess($meal->getPatient(), SecurityAction::VIEW_NUTRITION);

            $feedback->setData($this->mapper->mapEntityToResponse($meal))
                ->setFlushDescription("Repas récupéré avec succès.")
                ->autoInitFlush();

        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(int $id, MealRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $meal = $this->repository->find($id);
            if (!$meal) {
                throw new \InvalidArgumentException("Repas introuvable.");
            }

            $this->securityService->checkPatientAccess($meal->getPatient(), SecurityAction::MANAGE_MEAL);

            $meal = $this->mapper->mapRequestToEntity($dto, $meal);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($meal))
                ->setFlushDescription("Repas mis à jour avec succès.")
                ->autoInitFlush();

        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function delete(int $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $meal = $this->repository->find($id);
            if (!$meal) {
                throw new \InvalidArgumentException("Repas introuvable.");
            }

            $this->securityService->checkPatientAccess($meal->getPatient(), SecurityAction::MANAGE_MEAL);

            $this->entityManager->remove($meal);
            $this->entityManager->flush();

            $feedback->setFlushDescription("Repas supprimé avec succès.")
                ->autoInitFlush();

        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    /**
     * Résout le patient concerné :
     * - Si l'utilisateur connecté est un Patient : retourne directement ce patient (pas besoin de patientId).
     * - Si c'est un professionnel : exige et récupère le patient via le paramètre $patientId.
     */
    private function resolvePatientForUser($currentUser, ?int $patientId): Patient
    {
        if ($currentUser instanceof Patient) {
            return $currentUser;
        }

        // Pour un professionnel, le patientId est obligatoire
        if ($patientId === null) {
            throw new \InvalidArgumentException("L'identifiant du patient (patientId) est obligatoire pour un professionnel.");
        }

        $patient = $this->patientRepository->find($patientId);
        if (!$patient) {
            throw new \InvalidArgumentException("Patient introuvable.");
        }

        return $patient;
    }
}
