<?php

namespace App\Service\Appointment;

use App\DTO\Feedback;
use App\DTO\Request\Appointment\AppointmentRequestDTO;
use App\Entity\Appointment\AppointmentStatus;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\Patient;
use App\Mapper\Appointment\AppointmentMapper;
use App\Repository\Appointment\AppointmentRepository;
use App\Repository\Identity\PatientRepository;
use App\Repository\Identity\HealthcareProfessionalRepository;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Repository\Healthcare\HealthcareFacilityRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AppointmentService
{
    public function __construct(
        private readonly AppointmentRepository $repository,
        private readonly PatientRepository $patientRepository,
        private readonly HealthcareProfessionalRepository $professionalRepository,
        private readonly HealthcareOrganizationRepository $organizationRepository,
        private readonly HealthcareFacilityRepository $facilityRepository,
        private readonly AppointmentMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function getPatientAppointments(Patient $patient): Feedback
    {
        $feedback = new Feedback();
        try {
            $appointments = $this->repository->findBy(['patient' => $patient], ['scheduledAt' => 'DESC']);
            $responseDTOs = array_map(fn($app) => $this->mapper->mapEntityToResponse($app), $appointments);

            $feedback->setData($responseDTOs)
                ->setFlushDescription("Liste des rendez-vous récupérée avec succès.")
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }
        return $feedback;
    }

    public function getProfessionalOrAdminAppointments(HealthcareOrganization $organization): Feedback
    {
        $feedback = new Feedback();
        try {
            $this->securityService->checkOrganizationAccess($organization, SecurityAction::VIEW_APPOINTMENT);

            $appointments = $this->repository->findBy(['organization' => $organization], ['scheduledAt' => 'DESC']);
            $responseDTOs = array_map(fn($app) => $this->mapper->mapEntityToResponse($app), $appointments);

            $feedback->setData($responseDTOs)
                ->setFlushDescription("Liste des rendez-vous de l'organisation récupérée avec succès.")
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }
        return $feedback;
    }

    public function getConnectedProfessionalAppointments(): Feedback
    {
        $feedback = new Feedback();
        try {
            $currentUser = $this->securityService->getCurrentUser();

            // Correction : Utilisation de find() avec l'ID de l'utilisateur connecté
            $professional = $this->professionalRepository->find($currentUser->getId());

            if (!$professional) {
                return $feedback->setErrorFlushDescription("Utilisateur non associé à un profil professionnel.")->autoInitFlush();
            }

            // Filtrer les rendez-vous par ce professionnel
            $appointments = $this->repository->findBy(['professional' => $professional], ['scheduledAt' => 'DESC']);
            $responseDTOs = array_map(fn($app) => $this->mapper->mapEntityToResponse($app), $appointments);

            $feedback->setData($responseDTOs)
                ->setFlushDescription("Vos rendez-vous ont été récupérés avec succès.")
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }
        return $feedback;
    }

    public function create(AppointmentRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {

            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $professional = $this->professionalRepository->find($dto->professionalId);
            if (!$professional) {
                return $feedback->setErrorFlushDescription("Professionnel de santé introuvable.")->autoInitFlush();
            }

            $organization = $this->organizationRepository->find($dto->organizationId);
            if (!$organization) {
                return $feedback->setErrorFlushDescription("Organisation de santé introuvable.")->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess($organization, SecurityAction::CREATE_APPOINTMENT);

            $facility = null;
            if ($dto->facilityId) {
                $facility = $this->facilityRepository->find($dto->facilityId);
                if (!$facility) {
                    return $feedback->setErrorFlushDescription("Établissement (Facility) introuvable.")->autoInitFlush();
                }
            }

            $appointment = $this->mapper->mapRequestToEntity($dto, $patient, $professional, $organization, $facility);

            $this->entityManager->persist($appointment);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($appointment))
                ->setFlushDescription("Rendez-vous créé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(int $id, AppointmentRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();
        try {
            $appointment = $this->repository->find($id);
            if (!$appointment) {
                return $feedback->setErrorFlushDescription("Rendez-vous introuvable.")->autoInitFlush();
            }

            $organization = $this->organizationRepository->find($dto->organizationId);
            if (!$organization) {
                return $feedback->setErrorFlushDescription("Organisation de santé introuvable.")->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess($organization, SecurityAction::UPDATE_APPOINTMENT);

            $patient = $this->patientRepository->find($dto->patientId);
            $professional = $this->professionalRepository->find($dto->professionalId);

            $facility = null;
            if ($dto->facilityId) {
                $facility = $this->facilityRepository->find($dto->facilityId);
            }

            $updatedAppointment = $this->mapper->mapRequestToEntity($dto, $patient, $professional, $organization, $facility, $appointment);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($updatedAppointment))
                ->setFlushDescription("Rendez-vous mis à jour avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function cancel(int $id): Feedback
    {
        $feedback = new Feedback();
        try {
            $appointment = $this->repository->find($id);
            if (!$appointment) {
                return $feedback->setErrorFlushDescription("Rendez-vous introuvable.")->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess($appointment->getOrganization(), SecurityAction::CANCEL_APPOINTMENT);

            $appointment->setStatus(AppointmentStatus::CANCELLED);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($appointment))
                ->setFlushDescription("Rendez-vous annulé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function confirm(int $id): Feedback
    {
        $feedback = new Feedback();
        try {
            $appointment = $this->repository->find($id);
            if (!$appointment) {
                return $feedback->setErrorFlushDescription("Rendez-vous introuvable.")->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess($appointment->getOrganization(), SecurityAction::CONFIRM_APPOINTMENT);

            $appointment->setStatus(AppointmentStatus::CONFIRMED);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($appointment))
                ->setFlushDescription("Rendez-vous confirmé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function requestReschedule(int $id, \DateTimeImmutable $newDate): Feedback
    {
        $feedback = new Feedback();
        try {
            $appointment = $this->repository->find($id);
            if (!$appointment) {
                return $feedback->setErrorFlushDescription("Rendez-vous introuvable.")->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess($appointment->getOrganization(), SecurityAction::REQUEST_RESCHEDULE);

            // Mettre à jour la date planifiée et éventuellement changer le statut (ex: en attente de report)
            $appointment->setScheduledAt($newDate);
            $appointment->setStatus(AppointmentStatus::RESCHEDULE_REQUESTED); // Assurez-vous que ce statut existe dans votre Enum AppointmentStatus

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($appointment))
                ->setFlushDescription("Demande de report de rendez-vous enregistrée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function delete(int $id): Feedback
    {
        $feedback = new Feedback();
        try {
            $appointment = $this->repository->find($id);
            if (!$appointment) {
                return $feedback->setErrorFlushDescription("Rendez-vous introuvable.")->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess($appointment->getOrganization(), SecurityAction::DELETE_APPOINTMENT);

            // Suppression réelle (hard delete) puisque l'entité n'a pas de champ deletedAt
            $this->entityManager->remove($appointment);
            $this->entityManager->flush();

            $feedback->setFlushDescription("Rendez-vous supprimé avec succès.")->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
