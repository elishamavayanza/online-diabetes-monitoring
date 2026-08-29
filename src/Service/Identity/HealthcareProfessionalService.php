<?php

namespace App\Service\Identity;

use App\DTO\Feedback;
use App\DTO\Request\Identity\HealthcareProfessionalCreateRequestDTO;
use App\DTO\Request\Identity\HealthcareProfessionalUpdateRequestDTO;
use App\Entity\Common\UserStatus;
use App\Entity\Healthcare\OrganizationMembership;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Identity\ProfessionalType;
use App\Entity\Identity\Role;
use App\Mapper\Identity\HealthcareProfessionalMapper;
use App\Repository\Identity\HealthcareProfessionalRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use App\Service\File\FileUploaderService;
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
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly FileUploaderService $fileUploader
    ) {
    }

    /**
     * Liste tous les professionnels actifs.
     */
    public function getAll(): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(
                SecurityAction::VIEW->value
            );

            $professionals = $this->repository->findBy(
                ['deletedAt' => null],
                ['createdAt' => 'DESC']
            );

            $data = array_map(
                fn (HealthcareProfessional $professional) =>
                $this->mapper->mapEntityToResponse($professional),
                $professionals
            );

            return $feedback
                ->setData($data)
                ->setFlushDescription(
                    'Liste des professionnels récupérée avec succès.'
                )
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Accès refusé : ' . $e->getMessage()
                )
                ->autoInitFlush();

        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Erreur lors de la récupération des professionnels.'
                )
                ->autoInitFlush();
        }
    }

    /**
     * Récupère un professionnel par son ID.
     */
    public function getById(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(
                SecurityAction::VIEW->value
            );

            $professional = $this->repository->findOneBy([
                'id' => $id,
                'deletedAt' => null
            ]);

            if (!$professional) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Professionnel de santé introuvable.'
                    )
                    ->autoInitFlush();
            }

            return $feedback
                ->setData(
                    $this->mapper->mapEntityToResponse($professional)
                )
                ->setFlushDescription(
                    'Professionnel récupéré avec succès.'
                )
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Accès refusé : ' . $e->getMessage()
                )
                ->autoInitFlush();

        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Erreur lors de la récupération du professionnel.'
                )
                ->autoInitFlush();
        }
    }

    /**
     * Crée un compte professionnel de santé.
     */
    public function create(
        HealthcareProfessionalCreateRequestDTO $dto
    ): Feedback {
        $feedback = new Feedback();

        try {
            $currentUser = $this->securityService->getCurrentUser();
            $targetOrganization = null;

            foreach ($currentUser->getOrganizationMemberships() as $membership) {
                if ($membership->getStatus()->isActive() && $membership->getOrganization() !== null) {
                    $targetOrganization = $membership->getOrganization();
                    break;
                }
            }

            if (!$targetOrganization) {
                throw new AccessDeniedException('Aucune organisation active trouvée pour cet administrateur.');
            }

            $this->securityService->checkOrganizationAccess(
                $targetOrganization,
                SecurityAction::MANAGE_USERS
            );

            $existingUser = $this->repository->findOneBy([
                'email' => $dto->email
            ]);

            if ($existingUser) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Un utilisateur avec cet e-mail existe déjà.'
                    )
                    ->autoInitFlush();
            }

            /** @var HealthcareProfessional $professional */
            $professional = $this->mapper->mapCreateRequestToEntity($dto);

            // Gestion de l'upload de l'avatar à la création
            if ($dto->avatarFile) {
                $fileName = $this->fileUploader->upload($dto->avatarFile, 'avatars');
                $professional->setAvatarUrl($fileName);
            }

            $hashedPassword = $this->passwordHasher->hashPassword(
                $professional,
                $dto->password
            );

            $professional->setPasswordHash($hashedPassword);

            $professional->setStatus(
                UserStatus::PENDING_ACTIVATION
            );

            $role = $this->resolveRoleFromProfessionalType(
                ProfessionalType::from($dto->professionalType)
            );

            $professional->setRoles([
                $role->value
            ]);

            $this->entityManager->persist($professional);

            // Rattachement automatique à l'organisation via OrganizationMembership
            $orgMembership = new OrganizationMembership();
            $orgMembership->setUser($professional);
            $orgMembership->setOrganization($targetOrganization);
            $orgMembership->setStartDate(new \DateTimeImmutable());
            $orgMembership->setStatus(\App\Entity\Healthcare\MembershipStatus::ACTIVE);

            $professional->getOrganizationMemberships()->add($orgMembership);

            $this->entityManager->persist($orgMembership);
            $this->entityManager->flush();

            $responseDTO = $this->mapper->mapEntityToResponse(
                $professional
            );

            return $feedback
                ->setData($responseDTO)
                ->setFlushDescription(
                    sprintf(
                        'Professionnel créé avec succès avec le rôle %s et rattaché à l’organisation.',
                        $role->value
                    )
                )
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Accès refusé : ' . $e->getMessage()
                )
                ->autoInitFlush();

        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Erreur lors de la création du professionnel : '
                    . $e->getMessage()
                )
                ->autoInitFlush();
        }
    }

    /**
     * Détermine le rôle de sécurité à partir du type professionnel.
     */
    private function resolveRoleFromProfessionalType(
        ProfessionalType $professionalType
    ): Role {
        return match ($professionalType) {
            ProfessionalType::CLINICIAN => Role::ROLE_CLINICIAN,
            ProfessionalType::NUTRITIONIST => Role::ROLE_NUTRITIONIST,
        };
    }

    /**
     * Met à jour un professionnel.
     */
    public function update(
        string $id,
        HealthcareProfessionalUpdateRequestDTO $dto
    ): Feedback {
        $feedback = new Feedback();

        try {
            $currentUser = $this->securityService->getCurrentUser();

            $professional = $this->repository->findOneBy([
                'id' => $id,
                'deletedAt' => null
            ]);

            if (!$professional) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Professionnel de santé introuvable.'
                    )
                    ->autoInitFlush();
            }

            $isSelfUpdate = ($currentUser->getId() === $professional->getId());

            $targetOrganization = null;
            foreach ($currentUser->getOrganizationMemberships() as $membership) {
                if ($membership->getStatus()->isActive() && $membership->getOrganization() !== null) {
                    $targetOrganization = $membership->getOrganization();
                    break;
                }
            }

            if (!$targetOrganization) {
                throw new AccessDeniedException('Aucune organisation active trouvée.');
            }

            if (!$isSelfUpdate) {
                $this->securityService->checkOrganizationAccess(
                    $targetOrganization,
                    SecurityAction::MANAGE_USERS
                );
            }

            if ($dto->email !== null) {
                $existingUser = $this->repository->findOneBy([
                    'email' => $dto->email
                ]);

                if (
                    $existingUser
                    && $existingUser->getId() !== $professional->getId()
                ) {
                    return $feedback
                        ->setErrorFlushDescription(
                            'Cet e-mail est déjà utilisé par un autre utilisateur.'
                        )
                        ->autoInitFlush();
                }
            }

            $this->mapper->mapUpdateRequestToEntity(
                $dto,
                $professional
            );

            if ($dto->avatarFile) {
                if ($professional->getAvatarUrl()) {
                    $this->fileUploader->remove($professional->getAvatarUrl(), 'avatars');
                }

                $fileName = $this->fileUploader->upload($dto->avatarFile, 'avatars');
                $professional->setAvatarUrl($fileName);
            }

            if ($dto->professionalType !== null) {
                $role = $this->resolveRoleFromProfessionalType(
                    ProfessionalType::from($dto->professionalType)
                );

                $professional->setRoles([
                    $role->value
                ]);
            }

            if (!empty($dto->password)) {
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $professional,
                    $dto->password
                );

                $professional->setPasswordHash($hashedPassword);
            }

            $this->entityManager->flush();

            return $feedback
                ->setData(
                    $this->mapper->mapEntityToResponse($professional)
                )
                ->setFlushDescription(
                    'Professionnel mis à jour avec succès.'
                )
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Accès refusé : ' . $e->getMessage()
                )
                ->autoInitFlush();

        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Erreur lors de la mise à jour : '
                    . $e->getMessage()
                )
                ->autoInitFlush();
        }
    }

    /**
     * Supprime un professionnel (Soft Delete).
     */
    public function delete(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $currentUser = $this->securityService->getCurrentUser();
            $targetOrganization = null;

            foreach ($currentUser->getOrganizationMemberships() as $membership) {
                if ($membership->getStatus()->isActive() && $membership->getOrganization() !== null) {
                    $targetOrganization = $membership->getOrganization();
                    break;
                }
            }

            if (!$targetOrganization) {
                throw new AccessDeniedException('Aucune organisation active trouvée pour cet administrateur.');
            }

            $this->securityService->checkOrganizationAccess(
                $targetOrganization,
                SecurityAction::MANAGE_USERS
            );

            $professional = $this->repository->findOneBy([
                'id' => $id,
                'deletedAt' => null
            ]);

            if (!$professional) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Professionnel de santé introuvable.'
                    )
                    ->autoInitFlush();
            }

            $professional->setDeletedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            return $feedback
                ->setFlushDescription(
                    'Professionnel supprimé avec succès.'
                )
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Accès refusé : ' . $e->getMessage()
                )
                ->autoInitFlush();

        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Erreur lors de la suppression du professionnel.'
                )
                ->autoInitFlush();
        }
    }
}
