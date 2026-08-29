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
use App\Service\File\FileUploaderService;


class PatientService
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService,
        private readonly FileUploaderService $fileUploader
    ) {
    }

    /**
     * Récupère la liste de tous les patients actifs.
     */
    public function getAll(): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(
                SecurityAction::VIEW->value
            );

            // Récupère uniquement les instances de Patient non supprimées
            $patients = $this->repository->findBy(
                ['deletedAt' => null],
                ['createdAt' => 'DESC']
            );

            // Filtrer explicitement les instances de Patient si le repository renvoie tous types d'Users
            $patients = array_filter($patients, fn ($user) => $user instanceof Patient);

            $data = array_map(
                fn (Patient $patient) => PatientResponseDTO::fromEntity($patient),
                $patients
            );

            return $feedback
                ->setData(array_values($data))
                ->setFlushDescription('Liste des patients récupérée avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur lors de la récupération des patients : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }

    /**
     * Récupère le profil complet d'un patient.
     */
    public function getProfile(string $userId): Feedback
    {
        $feedback = new Feedback();

        try {
            $currentUser = $this->securityService->getCurrentUser();

            // 1. Recherche du patient
            $user = $this->repository->find($userId);
            if (!$user instanceof Patient) {
                return $feedback
                    ->setErrorFlushDescription('Profil patient introuvable.')
                    ->autoInitFlush();
            }

            // 2. Vérification des accès basée sur la logique métier
            // (au lieu de checkPermission qui est trop générique)
            $this->securityService->checkPatientAccess($user, SecurityAction::VIEW_PATIENT);

            return $feedback
                ->setData(PatientResponseDTO::fromEntity($user))
                ->setFlushDescription('Profil patient récupéré avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur lors de la récupération du profil : ' . $e->getMessage())
                ->autoInitFlush();
        }
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
            $currentUser = $this->securityService->getCurrentUser();

            // Sécurisation de la comparaison d'IDs (conversion en string pour éviter les erreurs de type Uuid/int)
            $isSelfUpdate = ($currentUser && (string) $currentUser->getId() === (string) $userId);

            if (!$isSelfUpdate) {
                $this->securityService->checkPermission(
                    SecurityAction::MANAGE_USERS->value
                );
            }

            $user = $this->repository->find($userId);

            if (!$user) {
                return $feedback->setErrorFlushDescription('Utilisateur introuvable.')->autoInitFlush();
            }

            if (!$user instanceof Patient) {
                return $feedback->setErrorFlushDescription('Cet utilisateur ne possède pas de profil patient.')->autoInitFlush();
            }

            $patient = $user;

            if ($dto->fullName !== null) {
                $patient->setFullName($dto->fullName);
            }

            if ($dto->phone !== null) {
                $patient->setPhone($dto->phone);
            }

            if ($dto->gender !== null) {
                $patient->setGender($dto->gender);
            }

            // Gestion de l'upload de l'avatar
            if ($dto->avatarFile !== null) {
                if ($patient->getAvatarUrl()) {
                    $this->fileUploader->remove($patient->getAvatarUrl(), 'avatars');
                }

                $fileName = $this->fileUploader->upload($dto->avatarFile, 'avatars');
                $patient->setAvatarUrl($fileName);
            }

            if ($dto->locale !== null) {
                $patient->setLocale($dto->locale);
            }

            if ($dto->dateOfBirth !== null) {
                $patient->setDateOfBirth(new \DateTime($dto->dateOfBirth));
            }

            if ($dto->placeOfBirth !== null) {
                $patient->setPlaceOfBirth($dto->placeOfBirth);
            }

            if ($dto->bloodType !== null) {
                $patient->setBloodType($dto->bloodType);
            }

            if ($dto->heightCm !== null) {
                $patient->setHeightCm($dto->heightCm);
            }

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
                ->setData(PatientResponseDTO::fromEntity($patient))
                ->setFlushDescription('Profil patient mis à jour avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback->setErrorFlushDescription('Erreur lors de la mise à jour du profil patient : ' . $e->getMessage())->autoInitFlush();
        }
    }
}
