<?php

namespace App\Service\Identity;

use App\DTO\Feedback;
use App\DTO\Request\Identity\AssignRoleRequestDTO;
use App\Entity\Identity\Role;
use App\Repository\Identity\UserRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserRoleService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function assignRole(
        string $userId,
        AssignRoleRequestDTO $dto
    ): Feedback {
        $feedback = new Feedback();

        try {
            /*
             * Seuls les utilisateurs ayant la permission
             * de gérer les rôles peuvent effectuer cette opération.
             */
            $this->securityService->checkPermission(
                SecurityAction::MANAGE_ROLES->value
            );

            $user = $this->userRepository->find($userId);

            if (!$user) {
                return $feedback
                    ->setErrorFlushDescription('Utilisateur introuvable.')
                    ->autoInitFlush();
            }

            /*
             * Vérification supplémentaire.
             */
            $role = Role::tryFrom($dto->role);

            if (!$role) {
                return $feedback
                    ->setErrorFlushDescription(
                        sprintf(
                            'Rôle invalide : %s',
                            $dto->role
                        )
                    )
                    ->autoInitFlush();
            }

            /*
             * On évite les doublons grâce à addRole().
             */
            $user->addRole($role->value);

            $this->entityManager->flush();

            $feedback
                ->setData([
                    'userId' => $user->getId(),
                    'roles' => $user->getRoles(),
                ])
                ->setFlushDescription(
                    'Rôle attribué avec succès.'
                )
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {

            $feedback
                ->setErrorFlushDescription(
                    'Accès refusé : ' . $e->getMessage()
                )
                ->autoInitFlush();

        } catch (\Exception $e) {

            $feedback
                ->setErrorFlushDescription(
                    'Erreur lors de l’attribution du rôle : ' . $e->getMessage()
                )
                ->autoInitFlush();
        }

        return $feedback;
    }

    public function removeRole(
        string $userId,
        string $role
    ): Feedback {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(
                SecurityAction::MANAGE_ROLES->value
            );

            $user = $this->userRepository->find($userId);

            if (!$user) {
                return $feedback
                    ->setErrorFlushDescription('Utilisateur introuvable.')
                    ->autoInitFlush();
            }

            $roleEnum = Role::tryFrom($role);

            if (!$roleEnum) {
                return $feedback
                    ->setErrorFlushDescription('Rôle invalide.')
                    ->autoInitFlush();
            }

            $roles = array_values(
                array_filter(
                    $user->getRoles(),
                    fn (string $currentRole) =>
                        $currentRole !== $roleEnum->value
                )
            );

            $user->setRoles($roles);

            $this->entityManager->flush();

            return $feedback
                ->setData([
                    'userId' => $user->getId(),
                    'roles' => $user->getRoles(),
                ])
                ->setFlushDescription(
                    'Rôle retiré avec succès.'
                )
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {

            return $feedback
                ->setErrorFlushDescription(
                    'Accès refusé : ' . $e->getMessage()
                )
                ->autoInitFlush();

        } catch (\Exception $e) {

            return $feedback
                ->setErrorFlushDescription(
                    'Erreur : ' . $e->getMessage()
                )
                ->autoInitFlush();
        }
    }
}
