<?php

namespace App\Service\Healthcare;

use App\DTO\Feedback;
use App\DTO\Request\Healthcare\CareTeamAssignmentRequestDTO;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Identity\Patient;
use App\Mapper\Healthcare\CareTeamAssignmentMapper;
use App\Repository\Healthcare\CareTeamAssignmentRepository;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Repository\Identity\HealthcareProfessionalRepository;
use App\Repository\Identity\PatientRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CareTeamAssignmentService
{
    public function __construct(
        private readonly CareTeamAssignmentRepository $assignmentRepository,
        private readonly PatientRepository $patientRepository,
        private readonly HealthcareProfessionalRepository $professionalRepository,
        private readonly HealthcareOrganizationRepository $organizationRepository,
        private readonly CareTeamAssignmentMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(string $organizationId, CareTeamAssignmentRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $organization = $this->findAndAuthorizeOrganization($organizationId);
            [$patient, $professional] = $this->findAndValidateAssignees($dto, $organization);

            if ($dto->active && $this->assignmentRepository->hasActiveAssignment(
                $patient,
                $professional,
                $organization,
                $dto->role
            )) {
                return $this->failure($feedback, 'Cette affectation active existe déjà.', 409);
            }

            $assignment = $this->mapper->mapRequestToEntity(
                $dto,
                $patient,
                $professional,
                $organization
            );

            $this->entityManager->persist($assignment);
            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($assignment))
                ->setFlushDescription('Affectation créée avec succès.')
                ->autoInitFlush()
                ->setStatus(201);
        } catch (AccessDeniedException $exception) {
            return $this->failure($feedback, 'Accès refusé : ' . $exception->getMessage(), 403);
        } catch (\DomainException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 404);
        } catch (\InvalidArgumentException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 422);
        } catch (\Throwable $exception) {
            throw $exception;
//            return $this->failure($feedback, 'Erreur lors de la création de l’affectation.', 500);
        }
    }

    public function list(string $organizationId): Feedback
    {
        $feedback = new Feedback();

        try {
            $organization = $this->findAndAuthorizeOrganization($organizationId);
            $assignments = $this->assignmentRepository->findByOrganization($organization);

            return $feedback
                ->setData(array_map($this->mapper->mapEntityToResponse(...), $assignments))
                ->setFlushDescription('Affectations récupérées avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $exception) {
            return $this->failure($feedback, 'Accès refusé : ' . $exception->getMessage(), 403);
        } catch (\DomainException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 404);
        }
    }

    public function get(string $organizationId, string $assignmentId): Feedback
    {
        $feedback = new Feedback();

        try {
            $organization = $this->findAndAuthorizeOrganization($organizationId);
            $assignment = $this->findAssignment($assignmentId, $organization);

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($assignment))
                ->setFlushDescription('Affectation récupérée avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $exception) {
            return $this->failure($feedback, 'Accès refusé : ' . $exception->getMessage(), 403);
        } catch (\DomainException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 404);
        }
    }

    public function update(
        string $organizationId,
        string $assignmentId,
        CareTeamAssignmentRequestDTO $dto
    ): Feedback {
        $feedback = new Feedback();

        try {
            $organization = $this->findAndAuthorizeOrganization($organizationId);
            $assignment = $this->findAssignment($assignmentId, $organization);
            [$patient, $professional] = $this->findAndValidateAssignees($dto, $organization);

            if ($dto->active && $this->assignmentRepository->hasActiveAssignment(
                $patient,
                $professional,
                $organization,
                $dto->role,
                $assignment->getId()
            )) {
                return $this->failure($feedback, 'Cette affectation active existe déjà.', 409);
            }

            $this->mapper->mapRequestToEntity(
                $dto,
                $patient,
                $professional,
                $organization,
                $assignment
            );
            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($assignment))
                ->setFlushDescription('Affectation mise à jour avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $exception) {
            return $this->failure($feedback, 'Accès refusé : ' . $exception->getMessage(), 403);
        } catch (\DomainException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 404);
        } catch (\InvalidArgumentException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 422);
        } catch (\Throwable $exception) {
            return $this->failure($feedback, 'Erreur lors de la mise à jour de l’affectation.', 500);
        }
    }

    public function delete(string $organizationId, string $assignmentId): Feedback
    {
        $feedback = new Feedback();

        try {
            $organization = $this->findAndAuthorizeOrganization($organizationId);
            $assignment = $this->findAssignment($assignmentId, $organization);

            $this->entityManager->remove($assignment);
            $this->entityManager->flush();

            return $feedback
                ->setFlushDescription('Affectation supprimée avec succès.')
                ->autoInitFlush()
                ->setStatus(204);
        } catch (AccessDeniedException $exception) {
            return $this->failure($feedback, 'Accès refusé : ' . $exception->getMessage(), 403);
        } catch (\DomainException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 404);
        } catch (\Throwable $exception) {
            return $this->failure($feedback, 'Erreur lors de la suppression de l’affectation.', 500);
        }
    }

    private function findAndAuthorizeOrganization(string $organizationId): HealthcareOrganization
    {
        $organization = $this->organizationRepository->find($organizationId);
        if (!$organization) {
            throw new \DomainException('Organisation introuvable.');
        }

        if (!$this->securityService->isOrganizationAdmin() && !$this->securityService->isSuperAdmin()) {
            throw new AccessDeniedException('Cette opération est réservée aux administrateurs d’organisation.');
        }

        $this->securityService->checkOrganizationAccess(
            $organization,
            SecurityAction::MANAGE_USERS
        );

        return $organization;
    }

    /** @return array{Patient, HealthcareProfessional} */
    private function findAndValidateAssignees(
        CareTeamAssignmentRequestDTO $dto,
        HealthcareOrganization $organization
    ): array {
        if ($dto->endDate !== null && $dto->endDate < $dto->startDate) {
            throw new \InvalidArgumentException('La date de fin doit être postérieure à la date de début.');
        }

        $patient = $this->patientRepository->find($dto->patientId);
        if (!$patient) {
            throw new \DomainException('Patient introuvable.');
        }

        $professional = $this->professionalRepository->find($dto->professionalId);
        if (!$professional) {
            throw new \DomainException('Professionnel introuvable.');
        }

        if (!$this->securityService->belongsToOrganization($patient, $organization)) {
            throw new AccessDeniedException('Le patient n’appartient pas à cette organisation.');
        }

        if (!$this->securityService->belongsToOrganization($professional, $organization)) {
            throw new AccessDeniedException('Le professionnel n’appartient pas à cette organisation.');
        }

        return [$patient, $professional];
    }

    private function findAssignment(
        string $assignmentId,
        HealthcareOrganization $organization
    ): \App\Entity\Healthcare\CareTeamAssignment {
        $assignment = $this->assignmentRepository->findOneBy([
            'id' => $assignmentId,
            'organization' => $organization,
        ]);

        if (!$assignment) {
            throw new \DomainException('Affectation introuvable dans cette organisation.');
        }

        return $assignment;
    }

    private function failure(Feedback $feedback, string $message, int $status): Feedback
    {
        return $feedback
            ->setErrorFlushDescription($message)
            ->autoInitFlush()
            ->setStatus($status);
    }
}
