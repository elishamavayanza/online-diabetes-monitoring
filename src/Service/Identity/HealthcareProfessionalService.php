<?php

namespace App\Service\Identity;

use App\DTO\Feedback;
use App\DTO\Request\Identity\HealthcareProfessionalRequestDTO;
use App\Mapper\Identity\HealthcareProfessionalMapper;
use App\Repository\Identity\HealthcareProfessionalRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HealthcareProfessionalService
{
    public function __construct(
        private readonly HealthcareProfessionalRepository $repository,
        private readonly HealthcareProfessionalMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function getAll(): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::VIEW->value);

            $professionals = $this->repository->findBy(
                ['deletedAt' => null],
                ['createdAt' => 'DESC']
            );

            $feedback->setData(array_map(
                fn ($professional) => $this->mapper->mapEntityToResponse($professional),
                $professionals
            ))->setFlushDescription('Liste des professionnels récupérée avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }

        return $feedback;
    }

    public function create(HealthcareProfessionalRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            // Contrôle d'accès RBAC pour la création
            $this->securityService->checkPermission(SecurityAction::MANAGE_USERS->value);

            $professional = $this->mapper->mapRequestToEntity($dto);

            $this->entityManager->persist($professional);
            $this->entityManager->flush();

            $responseDTO = $this->mapper->mapEntityToResponse($professional);

            $feedback->setData($responseDTO)
                ->setFlushDescription("Professionnel de santé créé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur lors de la création : " . $e->getMessage())
                ->autoInitFlush();
        }

        return $feedback;
    }

    public function getById(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::VIEW->value);

            $professional = $this->repository->findOneBy(['id' => $id, 'deletedAt' => null]);

            if (!$professional) {
                $feedback->setErrorFlushDescription("Professionnel de santé introuvable.")
                    ->autoInitFlush();
                return $feedback;
            }

            $feedback->setData($this->mapper->mapEntityToResponse($professional))
                ->setFlushDescription("Professionnel récupéré avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())
                ->autoInitFlush();
        }

        return $feedback;
    }

    public function update(string $id, HealthcareProfessionalRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_USERS->value);

            $professional = $this->repository->findOneBy(['id' => $id, 'deletedAt' => null]);
            if (!$professional) {
                $feedback->setErrorFlushDescription('Professionnel de santé introuvable.')
                    ->autoInitFlush();
                return $feedback;
            }

            $this->mapper->mapRequestToEntity($dto, $professional);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($professional))
                ->setFlushDescription('Professionnel de santé mis à jour avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription('Erreur lors de la mise à jour : ' . $e->getMessage())
                ->autoInitFlush();
        }

        return $feedback;
    }

    public function delete(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_USERS->value);

            $professional = $this->repository->findOneBy(['id' => $id, 'deletedAt' => null]);
            if (!$professional) {
                $feedback->setErrorFlushDescription('Professionnel de santé introuvable.')
                    ->autoInitFlush();
                return $feedback;
            }

            $this->entityManager->remove($professional);
            $this->entityManager->flush();

            $feedback->setFlushDescription('Professionnel de santé supprimé avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription('Erreur lors de la suppression : ' . $e->getMessage())
                ->autoInitFlush();
        }

        return $feedback;
    }
}
