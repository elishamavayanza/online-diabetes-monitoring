<?php

namespace App\Service\Treatment;

use App\DTO\Feedback;
use App\DTO\Request\Treatment\MedicationRequestDTO;
use App\Mapper\Treatment\MedicationMapper;
use App\Repository\Treatment\MedicationRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
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
            $this->checkMedicationAccess();

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

            $medication = $this->mapper->mapRequestToEntity($dto);

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

            $medication = $this->mapper->mapRequestToEntity($dto, $medication);

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
