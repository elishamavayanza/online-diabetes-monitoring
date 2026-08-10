<?php

namespace App\Service\Identity;

use App\DTO\Feedback;
use App\DTO\Request\Identity\HealthcareProfessionalRequestDTO;
use App\Mapper\Identity\HealthcareProfessionalMapper;
use App\Repository\Identity\HealthcareProfessionalRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HealthcareProfessionalService
{
    public function __construct(
        private readonly HealthcareProfessionalRepository $repository,
        private readonly HealthcareProfessionalMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService,
        private readonly UserPasswordHasherInterface $passwordHasher // 1. Injection du hacheur de mot de passe
    ) {}

    public function create(HealthcareProfessionalRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            // Contrôle d'accès RBAC pour la création
            $this->securityService->checkPermission(SecurityAction::MANAGE_USERS->value);

            $professional = $this->mapper->mapRequestToEntity($dto);

            // 2. Hachage sécurisé du mot de passe transmis par le DTO
            if (method_exists($dto, 'getPassword') && $dto->getPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $professional,
                    $dto->getPassword()
                );
                $professional->setPassword($hashedPassword);
            }

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

            $professional = $this->repository->find($id);

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
}
