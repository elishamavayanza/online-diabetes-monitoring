<?php

namespace App\Service\Identity;

use App\DTO\Feedback;
use App\DTO\Request\Identity\UserCreateRequestDTO;
use App\Entity\Common\Gender;
use App\Entity\Common\UserStatus;
use App\Entity\Healthcare\OrganizationMembership;
use App\Entity\Identity\Patient;
use App\Entity\Identity\Role;
use App\Mapper\Identity\UserMapper;
use App\Repository\Identity\UserRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserService
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly UserMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    /**
     * Crée un compte utilisateur (Patient).
     */
    public function create(UserCreateRequestDTO $dto): Feedback
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

            // Vérification de l'unicité de l'e-mail.
            if ($this->repository->findOneBy(['email' => $dto->email])) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Un utilisateur avec cet e-mail existe déjà.'
                    )
                    ->autoInitFlush();
            }

            $user = new Patient();

            $user
                ->setEmail($dto->email)
                ->setFullName($dto->fullName)
                ->setPhone($dto->phone ?? null)
                ->setGender($dto->gender !== null ? Gender::from($dto->gender) : null)
                ->setLocale($dto->locale ?? 'fr');

            $user->setPasswordHash(
                $this->passwordHasher->hashPassword(
                    $user,
                    $dto->password
                )
            );

            $user->setStatus(
                UserStatus::PENDING_ACTIVATION
            );

            $user->setRoles([
                Role::ROLE_PATIENT->value
            ]);

            $this->entityManager->persist($user);

            // Rattachement automatique du patient à l'organisation via OrganizationMembership
            $orgMembership = new OrganizationMembership();
            $orgMembership->setUser($user);
            $orgMembership->setOrganization($targetOrganization);
            $orgMembership->setStartDate(new \DateTimeImmutable());
            $orgMembership->setStatus(\App\Entity\Healthcare\MembershipStatus::ACTIVE);

            $user->getOrganizationMemberships()->add($orgMembership);

            $this->entityManager->persist($orgMembership);
            $this->entityManager->flush();

            return $feedback
                ->setData(
                    $this->mapper->mapEntityToResponse($user)
                )
                ->setFlushDescription(
                    'Compte utilisateur créé et rattaché à l’organisation avec succès.'
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
                    'Erreur lors de la création : ' . $e->getMessage()
                )
                ->autoInitFlush();
        }
    }
}
