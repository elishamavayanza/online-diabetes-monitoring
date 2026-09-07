<?php

namespace App\Service\Treatment;

use App\DTO\Feedback;
use App\DTO\Request\Treatment\InsulinRequestDTO;
use App\Mapper\Treatment\InsulinMapper;
use App\Repository\Treatment\InsulinRepository;
use App\Repository\Treatment\MedicationRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class InsulinService
{
    public function __construct(
        private readonly InsulinRepository $repository,
        private readonly MedicationRepository $medicationRepository,
        private readonly InsulinMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function all(): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::VIEW_MEDICATION->value);

            $insulins = $this->repository->findAll();
            $data = array_map(fn($i) => $this->mapper->mapEntityToResponse($i), $insulins);

            return $feedback
                ->setData($data)
                ->setFlushDescription('Liste des insulines récupérée avec succès.')
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
            $this->securityService->checkPermission(SecurityAction::VIEW_MEDICATION->value);

            $insulin = $this->repository->find($id);
            if (!$insulin) {
                return $feedback->setErrorFlushDescription('Insuline introuvable.')->autoInitFlush();
            }

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($insulin))
                ->setFlushDescription('Insuline récupérée avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }

    public function create(InsulinRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkProfessionalAccess(SecurityAction::MANAGE_MEDICATION);

            $medication = $this->medicationRepository->find($dto->medicationId);
            if (!$medication) {
                return $feedback->setErrorFlushDescription('Médicament introuvable.')->autoInitFlush();
            }

            $insulin = $this->mapper->mapRequestToEntity($dto, $medication);

            $this->entityManager->persist($insulin);
            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($insulin))
                ->setFlushDescription('Insuline créée avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }

    public function update(string $id, InsulinRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkProfessionalAccess(SecurityAction::MANAGE_MEDICATION);

            $insulin = $this->repository->find($id);
            if (!$insulin) {
                return $feedback->setErrorFlushDescription('Insuline introuvable.')->autoInitFlush();
            }

            $medication = $this->medicationRepository->find($dto->medicationId);
            if (!$medication) {
                return $feedback->setErrorFlushDescription('Médicament introuvable.')->autoInitFlush();
            }

            $insulin = $this->mapper->mapRequestToEntity($dto, $medication, $insulin);

            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($insulin))
                ->setFlushDescription('Insuline mise à jour avec succès.')
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

            $insulin = $this->repository->find($id);
            if (!$insulin) {
                return $feedback->setErrorFlushDescription('Insuline introuvable.')->autoInitFlush();
            }

            $this->entityManager->remove($insulin);
            $this->entityManager->flush();

            return $feedback
                ->setData(null)
                ->setFlushDescription('Insuline supprimée avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }
    }
}