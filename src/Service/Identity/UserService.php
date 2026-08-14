<?php

namespace App\Service\Identity;

use App\DTO\Feedback;
use App\DTO\Request\Identity\UserCreateRequestDTO;
use App\Entity\Common\Gender;
use App\Entity\Common\UserStatus;
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
     * Crée un compte utilisateur.
     *
     * Le compte est créé sans données métier spécifiques.
     * Dans le modèle actuel, un compte utilisateur destiné au patient
     * est représenté par l'entité Patient.
     *
     * Le profil patient sera complété séparément par PatientService.
     */
    public function create(UserCreateRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(
                SecurityAction::MANAGE_USERS->value
            );

            // Vérification de l'unicité de l'e-mail.
            if ($this->repository->findOneBy(['email' => $dto->email])) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Un utilisateur avec cet e-mail existe déjà.'
                    )
                    ->autoInitFlush();
            }

            /*
             * Dans ton héritage Doctrine actuel :
             *
             * User
             *   └── Patient
             *
             * User étant abstraite, nous devons utiliser une classe concrète.
             *
             * Ce compte n'est cependant pas encore un profil patient complet.
             */
            $user = new Patient();

            $user
                ->setEmail($dto->email)
                ->setFullName($dto->fullName)
                ->setPhone($dto->phone ?? null)
                ->setGender($dto->gender !== null ? Gender::from($dto->gender) : null)
                ->setLocale($dto->locale ?? 'fr');

            // Hash du mot de passe.
            $user->setPasswordHash(
                $this->passwordHasher->hashPassword(
                    $user,
                    $dto->password
                )
            );

            // État initial du compte.
            $user->setStatus(
                UserStatus::PENDING_ACTIVATION
            );

            /*
             * Le rôle définit le type d'accès.
             *
             * Ici, ce compte est destiné au patient.
             */
            $user->setRoles([
                Role::ROLE_PATIENT->value
            ]);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $feedback
                ->setData(
                    $this->mapper->mapEntityToResponse($user)
                )
                ->setFlushDescription(
                    'Compte utilisateur créé avec succès.'
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
                    'Erreur lors de la création du compte utilisateur : '
                    . $e->getMessage()
                )
                ->autoInitFlush();
        }
    }
}
