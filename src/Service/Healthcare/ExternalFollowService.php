<?php

namespace App\Service\Healthcare;

use App\DTO\Feedback;
use App\DTO\Request\Healthcare\ExternalFollowCloseRequestDTO;
use App\DTO\Request\Healthcare\ExternalFollowInvitationRequestDTO;
use App\DTO\Request\Healthcare\ExternalFollowRenewRequestDTO;
use App\DTO\Response\Healthcare\ExternalFollowInvitationResponseDTO;
use App\DTO\Response\Healthcare\ExternalFollowLogResponseDTO;
use App\Entity\Healthcare\CareTeamAssignment;
use App\Entity\Healthcare\CareTeamRole;
use App\Entity\Healthcare\ExternalFollowInvitation;
use App\Entity\Healthcare\ExternalFollowLog;
use App\Entity\Healthcare\ExternalFollowStatus;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Identity\Patient;
use App\Repository\Healthcare\CareTeamAssignmentRepository;
use App\Repository\Healthcare\ExternalFollowInvitationRepository;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Repository\Healthcare\OrganizationMembershipRepository;
use App\Repository\Identity\HealthcareProfessionalRepository;
use App\Repository\Identity\PatientRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use App\Service\Communication\ExternalFollowMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ExternalFollowService
{
    public function __construct(
        private readonly ExternalFollowInvitationRepository $invitationRepository,
        private readonly CareTeamAssignmentRepository $assignmentRepository,
        private readonly HealthcareOrganizationRepository $organizationRepository,
        private readonly HealthcareProfessionalRepository $professionalRepository,
        private readonly PatientRepository $patientRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService,
        private readonly ExternalFollowMailer $mailer,
        private readonly OrganizationMembershipRepository $membershipRepository,
        private readonly ExternalFollowAuditService $auditService
    ) {}

    /**
     * Crée une invitation et envoie un courriel au professionnel invité.
     */
    public function create(string $organizationId, ExternalFollowInvitationRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $organization = $this->findAndAuthorizeOrganization($organizationId);

            $patient = $this->patientRepository->find($dto->patientId);
            if (!$patient || $patient->isDeleted()) {
                throw new \DomainException('Patient introuvable.');
            }
            if (!$this->securityService->belongsToOrganization($patient, $organization)) {
                throw new AccessDeniedException('Le patient n’appartient pas à votre organisation.');
            }

            $professional = $this->findProfessionalByEmail($dto->email);
            if ($this->securityService->belongsToOrganization($professional, $organization)) {
                throw new \InvalidArgumentException(
                    'Ce professionnel appartient déjà à votre organisation : le suivi externe concerne une autre organisation.'
                );
            }

            if ($this->invitationRepository->findPendingBetween($organization, $patient, $professional) !== null) {
                throw new \DomainException('Une invitation en attente existe déjà pour ce professionnel et ce patient.', 409);
            }

            $invitation = new ExternalFollowInvitation();
            $invitation->setPatient($patient);
            $invitation->setOrganization($organization);
            $invitation->setInvitedBy($this->securityService->getCurrentUser());
            $invitation->setEmail($dto->email);
            $invitation->setProfessional($professional);
            $invitation->setToken(bin2hex(random_bytes(32)));
            $invitation->setStatus(ExternalFollowStatus::PENDING);
            $invitation->setMessage($dto->message);

            $today = new \DateTimeImmutable('today');

            if ($dto->endDate !== null) {
                // Période personnalisée « de telle à telle date ».
                $start = $dto->startDate ?? $today;

                if ($dto->startDate !== null && $dto->startDate < $today) {
                    throw new \InvalidArgumentException('La date de début ne peut pas être antérieure à aujourd’hui.');
                }
                if ($dto->endDate <= $start) {
                    throw new \InvalidArgumentException('La date de fin doit être postérieure à la date de début.');
                }
                if ($dto->startDate !== null && (int) $dto->endDate->diff($dto->startDate)->days > 730) {
                    throw new \InvalidArgumentException('La période ne peut pas dépasser 730 jours.');
                }

                $invitation->setStartDate($start);
                $invitation->setEndDate($dto->endDate);
            } else {
                if ($dto->durationDays === null) {
                    throw new \InvalidArgumentException(
                        'Indiquez une durée en jours ou une période personnalisée (de / à).'
                    );
                }

                $invitation->setStartDate($today);
                $invitation->setEndDate($today->modify('+' . $dto->durationDays . ' days'));
            }

            $this->entityManager->persist($invitation);
            $this->entityManager->flush();

            try {
                $this->mailer->sendInvitation($invitation);
            } catch (\Throwable) {
                // L'envoi du courriel ne bloque pas la création de l'invitation.
            }

            return $feedback
                ->setData(ExternalFollowInvitationResponseDTO::fromEntity($invitation))
                ->setFlushDescription('Invitation envoyée avec succès.')
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
        }
    }

    public function list(string $organizationId): Feedback
    {
        $feedback = new Feedback();

        try {
            $organization = $this->findAndAuthorizeOrganization($organizationId);
            $invitations = $this->invitationRepository->findByOrganization($organization);

            return $feedback
                ->setData(array_map(
                    ExternalFollowInvitationResponseDTO::fromEntity(...),
                    $invitations
                ))
                ->setFlushDescription('Invitations récupérées avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $exception) {
            return $this->failure($feedback, 'Accès refusé : ' . $exception->getMessage(), 403);
        }
    }

    /**
     * Prolonge le délai d'accès d'une invitation (et de l'affectation liée).
     */
    public function renew(
        string $organizationId,
        string $invitationId,
        ExternalFollowRenewRequestDTO $dto
    ): Feedback {
        $feedback = new Feedback();

        try {
            $organization = $this->findAndAuthorizeOrganization($organizationId);
            $invitation = $this->findInvitation($organization, $invitationId);

            if ($invitation->getStatus() === ExternalFollowStatus::REVOKED) {
                throw new \InvalidArgumentException('Cette invitation a été coupée et ne peut pas être renouvelée.');
            }
            if ($invitation->getStatus() === ExternalFollowStatus::DECLINED) {
                throw new \InvalidArgumentException('Cette invitation a été refusée et ne peut pas être renouvelée.');
            }

            $today = new \DateTimeImmutable('today');
            $currentEnd = $invitation->getEndDate();

            $base = ($currentEnd !== null && $currentEnd >= $today) ? $currentEnd : $today;

            if ($dto->endDate !== null) {
                // Nouvelle période personnalisée « de telle à telle date ».
                $start = $dto->startDate ?? $base;

                if ($dto->startDate !== null && $dto->startDate < $today) {
                    throw new \InvalidArgumentException('La date de début ne peut pas être antérieure à aujourd’hui.');
                }
                if ($dto->endDate <= $start) {
                    throw new \InvalidArgumentException('La date de fin doit être postérieure à la date de début.');
                }
                if ($dto->startDate !== null && (int) $dto->endDate->diff($dto->startDate)->days > 730) {
                    throw new \InvalidArgumentException('La période ne peut pas dépasser 730 jours.');
                }

                $invitation->setStartDate($start);
                $invitation->setEndDate($dto->endDate);
                $newEnd = $dto->endDate;
            } else {
                if ($dto->durationDays === null) {
                    throw new \InvalidArgumentException(
                        'Indiquez un nombre de jours à ajouter ou une nouvelle période (de / à).'
                    );
                }

                $newEnd = $base->modify('+' . $dto->durationDays . ' days');
                $invitation->setEndDate($newEnd);
            }

            if ($invitation->getStatus() !== ExternalFollowStatus::ACCEPTED) {
                $invitation->setStatus(ExternalFollowStatus::PENDING);
            }

            $assignment = $invitation->getAssignment();
            if ($assignment !== null) {
                if ($dto->endDate !== null && $dto->startDate !== null) {
                    $assignment->setStartDate($dto->startDate);
                }
                $assignment->setEndDate($newEnd);
                $assignment->setActive(true);
            }

            $this->entityManager->flush();

            try {
                if ($invitation->getStatus() === ExternalFollowStatus::ACCEPTED) {
                    $this->mailer->sendRenewal($invitation);
                } else {
                    $this->mailer->sendInvitation($invitation);
                }
            } catch (\Throwable) {
                // L'absence d'envoi ne bloque pas le renouvellement.
            }

            return $feedback
                ->setData(ExternalFollowInvitationResponseDTO::fromEntity($invitation))
                ->setFlushDescription('Délai prolongé avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $exception) {
            return $this->failure($feedback, 'Accès refusé : ' . $exception->getMessage(), 403);
        } catch (\DomainException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 404);
        } catch (\InvalidArgumentException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 422);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    /**
     * Coupe l'accès d'un professionnel externe immédiatement.
     */
    public function revoke(string $organizationId, string $invitationId): Feedback
    {
        $feedback = new Feedback();

        try {
            $organization = $this->findAndAuthorizeOrganization($organizationId);
            $invitation = $this->findInvitation($organization, $invitationId);

            if ($invitation->getStatus() === ExternalFollowStatus::DECLINED) {
                throw new \InvalidArgumentException('Cette invitation a déjà été refusée par le professionnel.');
            }

            if ($invitation->getStatus() !== ExternalFollowStatus::REVOKED) {
                $invitation->setStatus(ExternalFollowStatus::REVOKED);
                $invitation->setRevokedAt(new \DateTimeImmutable());

                $assignment = $invitation->getAssignment();
                if ($assignment !== null) {
                    $assignment->setActive(false);
                    $assignment->setEndDate(new \DateTimeImmutable('today'));
                }

                $this->entityManager->flush();

                try {
                    $this->mailer->sendRevoked($invitation);
                } catch (\Throwable) {
                    // L'absence d'envoi ne bloque pas la coupure.
                }
            }

            return $feedback
                ->setData(ExternalFollowInvitationResponseDTO::fromEntity($invitation))
                ->setFlushDescription('Accès coupé avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $exception) {
            return $this->failure($feedback, 'Accès refusé : ' . $exception->getMessage(), 403);
        } catch (\DomainException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 404);
        } catch (\InvalidArgumentException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 422);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    /**
     * Lecture publique d'une invitation par son jeton, pour la page d'acceptation.
     */
    public function getByToken(string $token): Feedback
    {
        $feedback = new Feedback();

        $invitation = $this->invitationRepository->findByToken($token);

        if (!$invitation) {
            return $feedback
                ->setErrorFlushDescription('Invitation introuvable.')
                ->autoInitFlush()
                ->setStatus(404);
        }

        return $feedback
            ->setData(ExternalFollowInvitationResponseDTO::fromEntity($invitation))
            ->setFlushDescription('Invitation récupérée avec succès.')
            ->autoInitFlush();
    }

    /**
     * Acceptation de l'invitation par le professionnel dont l'email correspond.
     */
    public function accept(string $token): Feedback
    {
        $feedback = new Feedback();

        try {
            $user = $this->securityService->getCurrentUser();
            if (!$user instanceof HealthcareProfessional) {
                throw new AccessDeniedException('Seul un professionnel de santé peut accepter cette invitation.');
            }

            $invitation = $this->invitationRepository->findByToken($token);
            if (!$invitation) {
                throw new \DomainException('Invitation introuvable.');
            }

            if (strcasecmp((string) $user->getEmail(), (string) $invitation->getEmail()) !== 0) {
                throw new AccessDeniedException(
                    'Cette invitation ne vous est pas destinée : l’email du compte connecté ne correspond pas.'
                );
            }

            if ($invitation->getStatus() === ExternalFollowStatus::REVOKED) {
                throw new \InvalidArgumentException('Cette invitation a été coupée par l’organisation émettrice.');
            }
            if ($invitation->getStatus() === ExternalFollowStatus::DECLINED) {
                throw new \InvalidArgumentException('Cette invitation a déjà été refusée.');
            }
            if (!$invitation->isActiveWindow(new \DateTimeImmutable('today'))) {
                throw new \InvalidArgumentException('Cette invitation a expiré.');
            }

            if ($invitation->getStatus() === ExternalFollowStatus::ACCEPTED) {
                return $feedback
                    ->setData(ExternalFollowInvitationResponseDTO::fromEntity($invitation))
                    ->setFlushDescription('Invitation déjà acceptée.')
                    ->autoInitFlush();
            }

            $invitation->setStatus(ExternalFollowStatus::ACCEPTED);
            $invitation->setAcceptedAt(new \DateTimeImmutable());

            $assignment = new CareTeamAssignment();
            $assignment->setPatient($invitation->getPatient());
            $assignment->setProfessional($user);
            $assignment->setOrganization($invitation->getOrganization());
            $assignment->setRole(CareTeamRole::EXTERNAL_FOLLOWER);
            $assignment->setStartDate($invitation->getStartDate() ?? new \DateTimeImmutable('today'));
            $assignment->setEndDate($invitation->getEndDate());
            $assignment->setActive(true);

            $this->entityManager->persist($assignment);
            $invitation->setAssignment($assignment);
            $this->entityManager->flush();

            try {
                $this->mailer->sendAcceptedConfirmation($invitation);
            } catch (\Throwable) {
                // L'absence d'envoi ne bloque pas l'acceptation.
            }

            return $feedback
                ->setData(ExternalFollowInvitationResponseDTO::fromEntity($invitation))
                ->setFlushDescription('Invitation acceptée, vous pouvez désormais suivre ce patient.')
                ->autoInitFlush();
        } catch (AccessDeniedException $exception) {
            return $this->failure($feedback, 'Accès refusé : ' . $exception->getMessage(), 403);
        } catch (\DomainException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 404);
        } catch (\InvalidArgumentException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 422);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    public function decline(string $token): Feedback
    {
        $feedback = new Feedback();

        try {
            $user = $this->securityService->getCurrentUser();
            if (!$user instanceof HealthcareProfessional) {
                throw new AccessDeniedException('Seul un professionnel de santé peut répondre à cette invitation.');
            }

            $invitation = $this->invitationRepository->findByToken($token);
            if (!$invitation) {
                throw new \DomainException('Invitation introuvable.');
            }

            if (strcasecmp((string) $user->getEmail(), (string) $invitation->getEmail()) !== 0) {
                throw new AccessDeniedException(
                    'Cette invitation ne vous est pas destinée : l’email du compte connecté ne correspond pas.'
                );
            }

            if ($invitation->getStatus() !== ExternalFollowStatus::PENDING) {
                throw new \InvalidArgumentException('Cette invitation ne peut plus être refusée.');
            }

            $invitation->setStatus(ExternalFollowStatus::DECLINED);
            $invitation->setDeclinedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            return $feedback
                ->setData(ExternalFollowInvitationResponseDTO::fromEntity($invitation))
                ->setFlushDescription('Invitation refusée.')
                ->autoInitFlush();
        } catch (AccessDeniedException $exception) {
            return $this->failure($feedback, 'Accès refusé : ' . $exception->getMessage(), 403);
        } catch (\DomainException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 404);
        } catch (\InvalidArgumentException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 422);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    /**
     * Liste les suivis externes actifs du professionnel connecté.
     */
    public function myFollows(): Feedback
    {
        $feedback = new Feedback();

        try {
            $user = $this->securityService->getCurrentUser();
            if (!$user instanceof HealthcareProfessional) {
                throw new AccessDeniedException('Cette page est réservée aux professionnels de santé.');
            }

            $invitations = $this->invitationRepository->findActiveByProfessional($user);

            return $feedback
                ->setData(array_map(
                    ExternalFollowInvitationResponseDTO::fromEntity(...),
                    $invitations
                ))
                ->setFlushDescription('Suivis récupérés avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $exception) {
            return $this->failure($feedback, 'Accès refusé : ' . $exception->getMessage(), 403);
        }
    }

    /**
     * Le professionnel externe ferme lui-même le suivi d'un patient (pour lui,
     * pas pour l'organisation). Le motif est notifié aux administrateurs de
     * l'organisation d'origine du patient.
     */
    public function closeMyFollow(string $invitationId, ExternalFollowCloseRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $user = $this->securityService->getCurrentUser();
            if (!$user instanceof HealthcareProfessional) {
                throw new AccessDeniedException('Cette action est réservée aux professionnels de santé.');
            }

            $invitation = $this->invitationRepository->findOneBy([
                'id' => $invitationId,
                'professional' => $user,
            ]);

            if (!$invitation || $invitation->isDeleted()) {
                throw new \DomainException('Suivi introuvable.');
            }

            if ($invitation->getStatus() === ExternalFollowStatus::CLOSED_BY_PROFESSIONAL) {
                throw new \InvalidArgumentException('Vous avez déjà fermé ce suivi.');
            }
            if ($invitation->getStatus() === ExternalFollowStatus::REVOKED) {
                throw new \InvalidArgumentException('Ce suivi a déjà été coupé par l’organisation émettrice.');
            }
            if ($invitation->getStatus() === ExternalFollowStatus::DECLINED) {
                throw new \InvalidArgumentException('Cette invitation a été refusée.');
            }
            if ($invitation->getStatus() === ExternalFollowStatus::PENDING) {
                throw new \InvalidArgumentException(
                    'Ce suivi n’est pas encore actif : il doit être accepté avant de pouvoir être fermé.'
                );
            }
            if ($invitation->getStatus() === ExternalFollowStatus::EXPIRED) {
                throw new \InvalidArgumentException('Ce suivi a expiré.');
            }

            $invitation->setStatus(ExternalFollowStatus::CLOSED_BY_PROFESSIONAL);
            $invitation->setClosureReason($dto->reason);
            $invitation->setClosedByProfessionalAt(new \DateTimeImmutable());

            $assignment = $invitation->getAssignment();
            if ($assignment !== null) {
                $assignment->setActive(false);
                $assignment->setEndDate(new \DateTimeImmutable('today'));
            }

            $this->entityManager->flush();

            // Journaliser la fermeture dans le dossier.
            try {
                $this->auditService->record(
                    $invitation->getPatient(),
                    $user,
                    $invitation,
                    SecurityAction::CLOSE_EXTERNAL_FOLLOW,
                    $dto->reason
                );
            } catch (\Throwable) {
                // L'enregistrement du journal ne bloque pas la fermeture.
            }

            // Notifier les administrateurs de l'organisation d'origine du patient.
            $admins = $this->membershipRepository->findActiveAdminsByOrganization($invitation->getOrganization());
            $adminEmails = array_values(array_filter(
                array_map(
                    static fn ($admin) => $admin->getEmail(),
                    $admins
                )
            ));

            if ($adminEmails === [] && $invitation->getInvitedBy()?->getEmail()) {
                $adminEmails = [$invitation->getInvitedBy()->getEmail()];
            }

            if ($adminEmails !== []) {
                try {
                    $this->mailer->sendSelfClosed($invitation, $adminEmails);
                } catch (\Throwable) {
                    // L'absence d'envoi ne bloque pas la fermeture.
                }
            }

            return $feedback
                ->setData(ExternalFollowInvitationResponseDTO::fromEntity($invitation))
                ->setFlushDescription('Suivi fermé avec succès. L’organisation d’origine en a été notifiée.')
                ->autoInitFlush();
        } catch (AccessDeniedException $exception) {
            return $this->failure($feedback, 'Accès refusé : ' . $exception->getMessage(), 403);
        } catch (\DomainException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 404);
        } catch (\InvalidArgumentException $exception) {
            return $this->failure($feedback, $exception->getMessage(), 422);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }
    public function logs(string $organizationId, ?string $invitationId = null): Feedback
    {
        $feedback = new Feedback();

        try {
            $organization = $this->findAndAuthorizeOrganization($organizationId);

            $query = $this->entityManager->createQueryBuilder()
                ->select('log')
                ->addSelect('pat')
                ->addSelect('pro')
                ->from(ExternalFollowLog::class, 'log')
                ->leftJoin('log.patient', 'pat')
                ->leftJoin('log.professional', 'pro')
                ->andWhere('log.organization = :organization')
                ->andWhere('log.deletedAt IS NULL')
                ->setParameter('organization', $organization)
                ->orderBy('log.createdAt', 'DESC');

            if ($invitationId !== null) {
                $query
                    ->andWhere('log.invitation = :invitationId')
                    ->setParameter('invitationId', $invitationId);
            }

            $logs = $query->getQuery()->getResult();

            return $feedback
                ->setData(array_map(
                    ExternalFollowLogResponseDTO::fromEntity(...),
                    $logs
                ))
                ->setFlushDescription('Journal des actions récupéré avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $exception) {
            return $this->failure($feedback, 'Accès refusé : ' . $exception->getMessage(), 403);
        }
    }

    private function findAndAuthorizeOrganization(string $organizationId): HealthcareOrganization
    {
        if (!$this->securityService->isOrganizationAdmin() && !$this->securityService->isSuperAdmin()) {
            throw new AccessDeniedException('Cette opération est réservée aux administrateurs d’organisation.');
        }

        $organization = $this->organizationRepository->find($organizationId);
        if (!$organization || $organization->isDeleted()) {
            throw new \DomainException('Organisation introuvable.');
        }

        $this->securityService->checkOrganizationAccess(
            $organization,
            SecurityAction::MANAGE_EXTERNAL_FOLLOW
        );

        return $organization;
    }

    private function findProfessionalByEmail(string $email): HealthcareProfessional
    {
        $professional = $this->professionalRepository->findOneBy(['email' => $email]);

        if (!$professional || $professional->isDeleted()) {
            throw new \InvalidArgumentException(
                'Aucun compte professionnel ne correspond à cet email. Le professionnel invité doit déjà disposer d’un compte.'
            );
        }

        return $professional;
    }

    private function findInvitation(
        HealthcareOrganization $organization,
        string $invitationId
    ): ExternalFollowInvitation {
        $invitation = $this->invitationRepository->findOneBy([
            'id' => $invitationId,
            'organization' => $organization,
        ]);

        if (!$invitation) {
            throw new \DomainException('Invitation introuvable dans cette organisation.');
        }

        return $invitation;
    }

    private function failure(Feedback $feedback, string $message, int $status): Feedback
    {
        return $feedback
            ->setErrorFlushDescription($message)
            ->autoInitFlush()
            ->setStatus($status);
    }
}