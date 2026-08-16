<?php

namespace App\Service\Patient;

use App\DTO\Feedback;
use App\DTO\Request\Patient\MedicalConsentRequestDTO;
use App\Mapper\Patient\MedicalConsentMapper;
use App\Repository\Identity\PatientRepository;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Repository\Patient\MedicalConsentRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MedicalConsentService
{
    public function __construct(
        private readonly MedicalConsentRepository $consentRepository,
        private readonly PatientRepository $patientRepository,
        private readonly HealthcareOrganizationRepository $organizationRepository,
        private readonly MedicalConsentMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function getByPatient(string $patientId): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_MEDICAL_CONSENT);

            $consents = $this->consentRepository->findBy(['patient' => $patient]);
            $responseDTOs = array_map([$this->mapper, 'mapEntityToResponse'], $consents);

            $feedback->setData($responseDTOs)
                ->setFlushDescription("Consentements récupérés avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function create(MedicalConsentRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $organization = null;
            if ($dto->organizationId) {
                $organization = $this->organizationRepository->find($dto->organizationId);
                if (!$organization) {
                    return $feedback->setErrorFlushDescription("Organisation introuvable.")->autoInitFlush();
                }
            }

            // Seul le patient (ou un admin/superadmin global) peut donner son propre consentement
            $this->securityService->checkPatientAccess($patient, SecurityAction::CREATE_MEDICAL_CONSENT);

            $consent = $this->mapper->mapRequestToEntity($dto, $patient, $organization);

            $this->entityManager->persist($consent);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($consent))
                ->setFlushDescription("Consentement médical enregistré avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(string $id, MedicalConsentRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $consent = $this->consentRepository->find($id);
            if (!$consent) {
                return $feedback->setErrorFlushDescription("Consentement médical introuvable.")->autoInitFlush();
            }

            $patient = $consent->getPatient();
            $this->securityService->checkPatientAccess($patient, SecurityAction::REVOKE_MEDICAL_CONSENT);

            $organization = null;
            if ($dto->organizationId) {
                $organization = $this->organizationRepository->find($dto->organizationId);
                if (!$organization) {
                    return $feedback->setErrorFlushDescription("Organisation introuvable.")->autoInitFlush();
                }
            }

            $this->mapper->mapRequestToEntity($dto, $patient, $organization, $consent);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($consent))
                ->setFlushDescription("Consentement médical mis à jour avec succès.")
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
            $consent = $this->consentRepository->find($id);
            if (!$consent) {
                return $feedback->setErrorFlushDescription("Consentement médical introuvable.")->autoInitFlush();
            }

            $patient = $consent->getPatient();
            $this->securityService->checkPatientAccess($patient, SecurityAction::REVOKE_MEDICAL_CONSENT);

            $this->entityManager->remove($consent);
            $this->entityManager->flush();

            $feedback->setData(null)
                ->setFlushDescription("Consentement médical supprimé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }


}
