<?php

namespace App\Service\Treatment;

use App\DTO\Feedback;
use App\DTO\Request\Treatment\MedicationRequestDTO;
use App\Entity\Treatment\Insulin;
use App\Entity\Treatment\InsulinType;
use App\Entity\Treatment\Medication;
use App\Entity\Treatment\MedicationClass;
use App\Mapper\Treatment\MedicationMapper;
use App\Repository\Treatment\MedicationRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MedicationService
{
    public function __construct(
        private readonly MedicationRepository $repository,
        private readonly MedicationMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    /**
     * Vérifie si l'utilisateur courant (Admin d'organisation ou Professionnel autorisé) peut gérer les médicaments.
     */
    private function checkMedicationAccess(): void
    {
        if (
            $this->securityService->isOrganizationAdmin() ||
            $this->securityService->isSuperAdmin()
        ) {
            return;
        }

        $this->securityService->checkProfessionalAccess(SecurityAction::MANAGE_MEDICATION);
    }

    /**
     * Vérifie que les champs spécifiques à l'insuline sont fournis pour la classe INSULIN.
     */
    private function isInsulinRequest(MedicationRequestDTO $dto): bool
    {
        return is_string($dto->category)
            ? $dto->category === MedicationClass::INSULIN->value
            : $dto->category === MedicationClass::INSULIN;
    }

    private function validateInsulinFields(MedicationRequestDTO $dto): ?string
    {
        if ($this->isInsulinRequest($dto)) {
            if (!$dto->insulinType || !$dto->concentration) {
                return 'Un médicament de classe INSULIN doit définir un type et une concentration d\'insuline.';
            }
            return null;
        }

        if (!$dto->form) {
            return 'Un médicament général doit définir une forme (comprimé ou liquide).';
        }
        return null;
    }

    /**
     * Synchronise le profil d'insuline associé au médicament selon sa classe.
     * - Classe INSULIN : crée ou met à jour l'entité Insulin liée.
     * - Autre classe : retire toute insuline liée.
     */
    private function syncInsulin(Medication $medication, MedicationRequestDTO $dto): void
    {
        $medication->setCategory(
            is_string($dto->category)
                ? MedicationClass::from($dto->category)
                : $dto->category
        );

        if (!$this->isInsulinRequest($dto)) {
            $medication->setForm(null);
            foreach ($medication->getInsulins() as $existing) {
                $medication->removeInsulin($existing);
            }
            return;
        }

        $insulin = $medication->getInsulins()->first() ?: null;
        if (!$insulin) {
            $insulin = new Insulin();
            $medication->addInsulin($insulin);
        }

        if ($dto->insulinType !== null) {
            $type = is_string($dto->insulinType)
                ? InsulinType::tryFrom($dto->insulinType)
                : $dto->insulinType;

            if ($type === null) {
                throw new InvalidArgumentException(sprintf(
                    "Le type d'insuline '%s' est invalide.",
                    $dto->insulinType
                ));
            }

            $insulin->setInsulinType($type);
        }

        if ($dto->concentration !== null) {
            $insulin->setConcentration($dto->concentration);
        }
    }

    public function all(): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->checkMedicationAccess();

            // Catalogue global : on récupère tous les médicaments sans filtrer par organisation
            $medications = $this->repository->findAll();
            $responseDTOs = array_map(fn($m) => $this->mapper->mapEntityToResponse($m), $medications);

            return $feedback
                ->setData($responseDTOs)
                ->setFlushDescription('Liste des médicaments récupérée avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }

    public function get(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            // Délègue la vérification au SecurityService global
            // qui autorise à la fois les rôles professionnels et le ROLE_PATIENT
            // pour l'action VIEW_MEDICATION.
            $this->securityService->checkPermission(SecurityAction::VIEW_MEDICATION->value);

            $medication = $this->repository->find($id);
            if (!$medication) {
                return $feedback->setErrorFlushDescription('Médicament introuvable.')->autoInitFlush();
            }

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($medication))
                ->setFlushDescription('Médicament récupéré avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }

    public function create(MedicationRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->checkMedicationAccess();

            $validationError = $this->validateInsulinFields($dto);
            if ($validationError !== null) {
                return $feedback
                    ->setErrorFlushDescription($validationError)
                    ->autoInitFlush();
            }

            $medication = $this->mapper->mapRequestToEntity($dto);
            $this->syncInsulin($medication, $dto);

            // Pas de setOrganization() puisque le catalogue est global
            $this->entityManager->persist($medication);
            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($medication))
                ->setFlushDescription('Médicament créé avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }

    public function update(string $id, MedicationRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->checkMedicationAccess();

            $medication = $this->repository->find($id);
            if (!$medication) {
                return $feedback->setErrorFlushDescription('Médicament introuvable.')->autoInitFlush();
            }

            $validationError = $this->validateInsulinFields($dto);
            if ($validationError !== null) {
                return $feedback
                    ->setErrorFlushDescription($validationError)
                    ->autoInitFlush();
            }

            $medication = $this->mapper->mapRequestToEntity($dto, $medication);
            $this->syncInsulin($medication, $dto);

            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($medication))
                ->setFlushDescription('Médicament mis à jour avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }

    public function delete(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->checkMedicationAccess();

            $medication = $this->repository->find($id);
            if (!$medication) {
                return $feedback->setErrorFlushDescription('Médicament introuvable.')->autoInitFlush();
            }

            $this->entityManager->remove($medication);
            $this->entityManager->flush();

            return $feedback
                ->setData(null)
                ->setFlushDescription('Médicament supprimé avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }
}
