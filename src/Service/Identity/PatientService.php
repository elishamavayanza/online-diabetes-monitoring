<?php

namespace App\Service\Identity;

use App\DTO\Feedback;
use App\DTO\Request\Identity\PatientRequestDTO;
use App\DTO\Response\Identity\PatientResponseDTO;
use App\Entity\Identity\Address;
use App\Entity\Identity\Patient;
use App\Repository\Identity\UserRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class PatientService
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {
    }

    /**
     * Complète ou met à jour le profil métier d'un patient.
     *
     * Le compte utilisateur doit déjà exister.
     */
    public function updateProfile(
        string $userId,
        PatientRequestDTO $dto
    ): Feedback {
        $feedback = new Feedback();

        try {
            // 1. Récupérer l'utilisateur actuellement connecté
            $currentUser = $this->securityService->getCurrentUser();

            // 2. Vérifier si c'est le patient lui-même qui modifie son profil
            $isSelfUpdate = ($currentUser && $currentUser->getId() === $userId);

            // 3. Si ce n'est pas lui-même, exiger la permission d'administration
            if (!$isSelfUpdate) {
                $this->securityService->checkPermission(
                    SecurityAction::MANAGE_USERS->value
                );
            }

            /*
             * On récupère l'utilisateur existant.
             */
            $user = $this->repository->find($userId);

            if (!$user) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Utilisateur introuvable.'
                    )
                    ->autoInitFlush();
            }

            /*
             * Le profil patient nécessite un utilisateur
             * de type Patient.
             */
            if (!$user instanceof Patient) {
                return $feedback
                    ->setErrorFlushDescription(
                        'Cet utilisateur ne possède pas de profil patient.'
                    )
                    ->autoInitFlush();
            }

            $patient = $user;

            /*
             * Données du profil personnel.
             * (L'email et le mot de passe sont volontairement exclus pour des raisons de sécurité)
             */
            if ($dto->fullName !== null) {
                $patient->setFullName($dto->fullName);
            }

            if ($dto->phone !== null) {
                $patient->setPhone($dto->phone);
            }

            if ($dto->gender !== null) {
                $patient->setGender($dto->gender);
            }

            if ($dto->avatarUrl !== null) {
                $patient->setAvatarUrl($dto->avatarUrl);
            }

            if ($dto->locale !== null) {
                $patient->setLocale($dto->locale);
            }

            /*
             * Informations spécifiques au patient.
             */
            if ($dto->dateOfBirth !== null) {
                $patient->setDateOfBirth(
                    new \DateTime($dto->dateOfBirth)
                );
            }

            if ($dto->placeOfBirth !== null) {
                $patient->setPlaceOfBirth(
                    $dto->placeOfBirth
                );
            }

            if ($dto->bloodType !== null) {
                $patient->setBloodType(
                    $dto->bloodType
                );
            }

            if ($dto->heightCm !== null) {
                $patient->setHeightCm(
                    $dto->heightCm
                );
            }

            /*
             * Adresse embarquée.
             */
            if (
                $dto->street !== null ||
                $dto->city !== null ||
                $dto->postalCode !== null ||
                $dto->country !== null ||
                $dto->state !== null
            ) {
                $address = $patient->getAddress();

                if (!$address) {
                    $address = new Address();
                    $patient->setAddress($address);
                }

                if ($dto->street !== null) {
                    $address->setStreet($dto->street);
                }

                if ($dto->city !== null) {
                    $address->setCity($dto->city);
                }

                if ($dto->postalCode !== null) {
                    $address->setPostalCode($dto->postalCode);
                }

                if ($dto->country !== null) {
                    $address->setCountry($dto->country);
                }

                if ($dto->state !== null) {
                    $address->setState($dto->state);
                }
            }

            $this->entityManager->flush();

            return $feedback
                ->setData(
                    PatientResponseDTO::fromEntity($patient)
                )
                ->setFlushDescription(
                    'Profil patient mis à jour avec succès.'
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
                    'Erreur lors de la mise à jour du profil patient : '
                    . $e->getMessage()
                )
                ->autoInitFlush();
        }
    }
}
