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

    public function all(): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkProfessionalAccess(SecurityAction::MANAGE_MEDICATION);

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
            $this->securityService->checkProfessionalAccess(SecurityAction::MANAGE_MEDICATION);

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
            $this->securityService->checkProfessionalAccess(SecurityAction::MANAGE_MEDICATION);

            $medication = $this->mapper->mapRequestToEntity($dto);

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
            $this->securityService->checkProfessionalAccess(SecurityAction::MANAGE_MEDICATION);

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
            $this->securityService->checkProfessionalAccess(SecurityAction::MANAGE_MEDICATION);

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
