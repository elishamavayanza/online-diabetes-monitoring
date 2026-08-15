<?php

namespace App\Service\Patient;

use App\DTO\Feedback;
use App\DTO\Request\Patient\AllergyRequestDTO;
use App\Entity\Patient\Allergy;
use App\Mapper\Patient\AllergyMapper;
use App\Repository\Identity\PatientRepository;
use App\Repository\Patient\AllergyRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AllergyService
{
    public function __construct(
        private readonly AllergyRepository $allergyRepository,
        private readonly PatientRepository $patientRepository,
        private readonly AllergyMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(AllergyRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::CREATE_ALLERGY);

            $allergy = $this->mapper->mapRequestToEntity($dto, $patient);

            $this->entityManager->persist($allergy);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($allergy))
                ->setFlushDescription("Allergie enregistrée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function get(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $allergy = $this->allergyRepository->find($id);
            if (!$allergy) {
                return $feedback->setErrorFlushDescription("Allergie introuvable.")->autoInitFlush();
            }

            $patient = $allergy->getPatient();
            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_ALLERGY);

            $feedback->setData($this->mapper->mapEntityToResponse($allergy))
                ->setFlushDescription("Allergie récupérée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function getByPatient(string $patientId): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_ALLERGY);

            $allergies = $this->allergyRepository->findBy(['patient' => $patient]);
            $responseDTOs = array_map([$this->mapper, 'mapEntityToResponse'], $allergies);

            $feedback->setData($responseDTOs)
                ->setFlushDescription("Liste des allergies récupérée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(string $id, AllergyRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $allergy = $this->allergyRepository->find($id);
            if (!$allergy) {
                return $feedback->setErrorFlushDescription("Allergie introuvable.")->autoInitFlush();
            }

            $patient = $allergy->getPatient();
            $this->securityService->checkPatientAccess($patient, SecurityAction::UPDATE_ALLERGY);

            $this->mapper->mapRequestToEntity($dto, $patient, $allergy);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($allergy))
                ->setFlushDescription("Allergie mise à jour avec succès.")
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
            $allergy = $this->allergyRepository->find($id);
            if (!$allergy) {
                return $feedback->setErrorFlushDescription("Allergie introuvable.")->autoInitFlush();
            }

            $patient = $allergy->getPatient();
            $this->securityService->checkPatientAccess($patient, SecurityAction::DELETE_ALLERGY);

            $this->entityManager->remove($allergy);
            $this->entityManager->flush();

            $feedback->setData(null)
                ->setFlushDescription("Allergie supprimée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
