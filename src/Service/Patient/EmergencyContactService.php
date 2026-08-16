<?php

namespace App\Service\Patient;

use App\DTO\Feedback;
use App\DTO\Request\Patient\EmergencyContactRequestDTO;
use App\Mapper\Patient\EmergencyContactMapper;
use App\Repository\Identity\PatientRepository;
use App\Repository\Patient\EmergencyContactRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class EmergencyContactService
{
    public function __construct(
        private readonly EmergencyContactRepository $contactRepository,
        private readonly PatientRepository $patientRepository,
        private readonly EmergencyContactMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(EmergencyContactRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::CREATE_EMERGENCY_CONTACT);

            $contact = $this->mapper->mapRequestToEntity($dto, $patient);

            $this->entityManager->persist($contact);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($contact))
                ->setFlushDescription("Contact d'urgence ajouté avec succès.")
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

            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_EMERGENCY_CONTACT);

            $contacts = $this->contactRepository->findBy(['patient' => $patient]);
            $responseDTOs = array_map([$this->mapper, 'mapEntityToResponse'], $contacts);

            $feedback->setData($responseDTOs)
                ->setFlushDescription("Contacts d'urgence récupérés avec succès.")
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
            $contact = $this->contactRepository->find($id);
            if (!$contact) {
                return $feedback->setErrorFlushDescription("Contact d'urgence introuvable.")->autoInitFlush();
            }

            $patient = $contact->getPatient();
            $this->securityService->checkPatientAccess($patient, SecurityAction::DELETE_EMERGENCY_CONTACT);

            $this->entityManager->remove($contact);
            $this->entityManager->flush();

            $feedback->setData(null)
                ->setFlushDescription("Contact d'urgence supprimé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
    public function update(string $id, EmergencyContactRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $contact = $this->contactRepository->find($id);
            if (!$contact) {
                return $feedback->setErrorFlushDescription("Contact d'urgence introuvable.")->autoInitFlush();
            }

            $patient = $contact->getPatient();
            $this->securityService->checkPatientAccess($patient, SecurityAction::UPDATE_EMERGENCY_CONTACT);

            // Mise à jour de l'entité via le mapper
            $this->mapper->mapRequestToEntity($dto, $patient, $contact);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($contact))
                ->setFlushDescription("Contact d'urgence mis à jour avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
