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
            $this->securityService->checkPermission(SecurityAction::VIEW_PATIENT->value);

            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_PATIENT);

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
}
