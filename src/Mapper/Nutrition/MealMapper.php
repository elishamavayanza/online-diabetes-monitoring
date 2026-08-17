<?php

namespace App\Mapper\Nutrition;

use App\DTO\Request\Nutrition\MealRequestDTO;
use App\DTO\Response\Nutrition\MealResponseDTO;
use App\Entity\Identity\Patient;
use App\Entity\Nutrition\Meal;
use App\Entity\Nutrition\MealType;
use App\Repository\Identity\PatientRepository;
use App\Security\SecurityServiceInterface;

class MealMapper
{
    public function __construct(
        private readonly SecurityServiceInterface $securityService,
        private readonly PatientRepository $patientRepository
    ) {}

    public function mapRequestToEntity(MealRequestDTO $dto, ?Meal $meal = null): Meal
    {
        $meal ??= new Meal();

        $meal->setName($dto->name);
        $meal->setDescription($dto->description);

        $currentUser = $this->securityService->getCurrentUser();

        // 1. Gestion de l'émetteur (issuer) obligatoire en base de données
        if (method_exists($meal, 'setIssuer') && $currentUser !== null) {
            $meal->setIssuer($currentUser);
        }

        // 2. Gestion du patient
        if ($meal->getPatient() === null) {
            if ($currentUser instanceof Patient) {
                // Si c'est un patient connecté, il s'auto-attribue le repas
                $meal->setPatient($currentUser);
            } else {
                // Si c'est un professionnel, il doit fournir un patientId
                if ($dto->patientId === null) {
                    throw new \InvalidArgumentException("L'identifiant du patient (patientId) est obligatoire.");
                }

                $patient = $this->patientRepository->find($dto->patientId);
                if (!$patient) {
                    throw new \InvalidArgumentException("Patient introuvable.");
                }

                $meal->setPatient($patient);
            }
        }

        // 3. Date de mesure
        $meal->setMeasuredAt($dto->measuredAt ?? new \DateTimeImmutable());

        if ($dto->mealType !== null) {
            $meal->setMealType(is_string($dto->mealType) ? MealType::tryFrom($dto->mealType) : $dto->mealType);
        }

        return $meal;
    }

    public function mapEntityToResponse(Meal $meal): MealResponseDTO
    {
        return MealResponseDTO::fromEntity($meal);
    }
}
