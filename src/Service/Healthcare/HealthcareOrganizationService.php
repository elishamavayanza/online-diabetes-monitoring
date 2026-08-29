<?php

namespace App\Service\Healthcare;

use App\DTO\Feedback;
use App\DTO\Request\Healthcare\HealthcareOrganizationRequestDTO;
use App\Mapper\Healthcare\HealthcareOrganizationMapper;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Service\File\FileUploaderService;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HealthcareOrganizationService
{
    public function __construct(
        private readonly HealthcareOrganizationRepository $repository,
        private readonly HealthcareOrganizationMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService,
        private readonly FileUploaderService $fileUploaderService
    ) {}

    public function getById(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $organization = $this->repository->find($id);
            if (!$organization) {
                $feedback->setErrorFlushDescription("Organisation de santé introuvable.")->autoInitFlush();
                return $feedback;
            }

            $feedback->setData($this->mapper->mapEntityToResponse($organization))
                ->setFlushDescription("Organisation récupérée avec succès.")
                ->autoInitFlush();

        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function getPaginated(int $page, int $limit): Feedback
    {
        $feedback = new Feedback();

        try {
            $organizations = $this->repository->findPaginated($page, $limit);
            $total = $this->repository->count([]);

            $data = array_map(
                fn($org) => $this->mapper->mapEntityToResponse($org),
                $organizations
            );

            // Structure paginée renvoyée dans le feedback
            $feedback->setData([
                'items' => $data,
                'pagination' => [
                    'currentPage' => $page,
                    'limit' => $limit,
                    'totalItems' => $total,
                    'totalPages' => ceil($total / $limit)
                ]
            ])->setFlushDescription("Liste des organisations récupérée avec succès.")
                ->autoInitFlush();

        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function create(HealthcareOrganizationRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_ORGANIZATION->value);

            $organization = $this->mapper->mapRequestToEntity($dto);

            // Gestion de l'upload du logo s'il est présent
            if ($dto->logoFile) {
                // Upload du fichier dans un sous-dossier "organizations" par exemple
                $fileName = $this->fileUploaderService->upload($dto->logoFile, 'organizations');

                // Supposons que votre entité possède une méthode setLogoUrl ou setLogo
                // Stockage du chemin d'accès ou du nom du fichier
                $organization->setLogoUrl('/uploads/files/organizations/' . $fileName);
            }

            $this->entityManager->persist($organization);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($organization))
                ->setFlushDescription("Organisation de santé créée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(string $id, HealthcareOrganizationRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_ORGANIZATION->value);

            $organization = $this->repository->find($id);
            if (!$organization) {
                $feedback->setErrorFlushDescription("Organisation de santé introuvable.")->autoInitFlush();
                return $feedback;
            }

            // Utilisation du mapper pour mettre à jour les propriétés textuelles
            $this->mapper->mapRequestToEntity($dto, $organization);

            // Gestion de l'upload d'un nouveau logo s'il est présent
            if ($dto->logoFile) {
                // Optionnel : Supprimer l'ancien logo physique si l'entité en possédait un
                if ($organization->getLogoUrl()) {
                    $oldFileName = basename($organization->getLogoUrl());
                    $this->fileUploaderService->remove($oldFileName, 'organizations');
                }

                // Upload du nouveau fichier
                $fileName = $this->fileUploaderService->upload($dto->logoFile, 'organizations');
                $organization->setLogoUrl('/uploads/files/organizations/' . $fileName);
            }

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($organization))
                ->setFlushDescription("Organisation de santé mise à jour avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function delete(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_ORGANIZATION->value);

            $organization = $this->repository->find($id);
            if (!$organization) {
                $feedback->setErrorFlushDescription("Organisation de santé introuvable.")->autoInitFlush();
                return $feedback;
            }

            $this->entityManager->remove($organization);
            $this->entityManager->flush();

            $feedback->setFlushDescription("Organisation de santé supprimée avec succès.")->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function suspend(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_ORGANIZATION->value);

            $organization = $this->repository->find($id);
            if (!$organization) {
                $feedback->setErrorFlushDescription("Organisation de santé introuvable.")->autoInitFlush();
                return $feedback;
            }

            // Supposons que votre entité possède un booléen ou un statut "active"
            $organization->setActive(false);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($organization))
                ->setFlushDescription("Organisation de santé suspendue avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
