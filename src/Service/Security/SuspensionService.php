<?php

namespace App\Service\Security;

use App\DTO\Feedback;
use App\DTO\Request\Security\SuspensionRequestDTO;
use App\Entity\Appointment\ReminderChannel;
use App\Entity\Common\UserStatus;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Healthcare\MembershipStatus;
use App\Entity\Identity\HealthcareProfessional;
use App\Entity\Identity\Patient;
use App\Entity\Identity\User;
use App\Entity\Notification\NotificationType;
use App\Entity\Security\AccountSuspension;
use App\Entity\Security\SuspensionScope;
use App\Repository\Healthcare\OrganizationMembershipRepository;
use App\Repository\Security\AccountSuspensionRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use App\Service\Notification\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Logique de suspension / réactivation :
 *  - suspension d'une organisation par le ROOT (motif + délai) avec notification
 *    par email des administrateurs et de tous les membres ;
 *  - suspension d'un professionnel ou d'un patient par l'administrateur
 *    d'organisation (motif + délai) avec notification par email ;
 *  - blocage de l'accès à la plateforme tant que la suspension court ;
 *  - levée manuelle (réactivation) ou automatique à l'échéance.
 */
class SuspensionService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService,
        private readonly AccountSuspensionRepository $suspensionRepository,
        private readonly OrganizationMembershipRepository $membershipRepository,
        private readonly SuspensionMailer $suspensionMailer,
        private readonly NotificationService $notificationService
    ) {}

    /*
     * ============================================================
     * SUSPENSION D'ORGANISATION (ROOT)
     * ============================================================
     */

    public function suspendOrganization(string $organizationId, SuspensionRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            if (!$this->securityService->isSuperAdmin()) {
                throw new AccessDeniedException('Seul le super administrateur peut suspendre une organisation.');
            }

            $organization = $this->findOrganization($organizationId);
            if ($organization === null) {
                $feedback->setErrorFlushDescription('Organisation de santé introuvable.')->autoInitFlush();
                return $feedback;
            }

            if (!$organization->isActive()) {
                $feedback->setErrorFlushDescription('Cette organisation est déjà suspendue.')->autoInitFlush();
                return $feedback;
            }

            [$startsAt, $endsAt] = $this->resolveDates($dto, $feedback);
            if ($feedback->hasErrors()) {
                return $feedback;
            }

            $suspension = (new AccountSuspension())
                ->setScope(SuspensionScope::ORGANIZATION)
                ->setOrganization($organization)
                ->setReason($dto->reason)
                ->setStartsAt($startsAt)
                ->setEndsAt($endsAt)
                ->setCreatedBy($this->securityService->getCurrentUser());

            $this->entityManager->persist($suspension);
            $organization->setActive(false);
            $this->entityManager->flush();

            $recipients = $this->getOrganizationRecipients($organization);
            foreach ($recipients as $recipient) {
                $this->notifyOrganizationSuspension($recipient, $organization, $suspension);
            }

            $feedback->setData([
                'id' => $organization->getId(),
                'active' => false,
                'notified' => count($recipients),
            ])
                ->setFlushDescription(sprintf(
                    'Organisation suspendue avec succès. %d personne(s) notifiée(s).',
                    count($recipients)
                ))
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function reactivateOrganization(string $organizationId): Feedback
    {
        $feedback = new Feedback();

        try {
            if (!$this->securityService->isSuperAdmin()) {
                throw new AccessDeniedException('Seul le super administrateur peut réactiver une organisation.');
            }

            $organization = $this->findOrganization($organizationId);
            if ($organization === null) {
                $feedback->setErrorFlushDescription('Organisation de santé introuvable.')->autoInitFlush();
                return $feedback;
            }

            if ($organization->isActive()) {
                $feedback->setErrorFlushDescription('Cette organisation n’est pas suspendue.')->autoInitFlush();
                return $feedback;
            }

            $now = new \DateTimeImmutable();
            $suspensions = $this->suspensionRepository->findActiveForOrganization($organization);
            foreach ($suspensions as $suspension) {
                $suspension->setCanceledAt($now);
            }

            $organization->setActive(true);
            $this->entityManager->flush();

            $recipients = $this->getOrganizationRecipients($organization);
            foreach ($recipients as $recipient) {
                $reference = $suspensions[0] ?? null;
                if ($reference !== null) {
                    $this->suspensionMailer->sendOrganizationReactivated($recipient, $organization, $reference);
                }
                $this->notifyInApp($recipient, 'Organisation réactivée', sprintf(
                    'L’organisation « %s » est de nouveau active.', $organization->getName()
                ));
            }

            $feedback->setData([
                'id' => $organization->getId(),
                'active' => true,
                'notified' => count($recipients),
            ])
                ->setFlushDescription('Organisation réactivée avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    /*
     * ============================================================
     * SUSPENSION D'UN COMPTE (ADMINISTRATEUR D'ORGANISATION)
     * ============================================================
     */

    public function suspendUser(string $userId, SuspensionRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $target = $this->findUser($userId);
            if ($target === null) {
                $feedback->setErrorFlushDescription('Utilisateur introuvable.')->autoInitFlush();
                return $feedback;
            }

            $scope = $this->scopeFor($target);
            $organization = $this->resolveActingOrganization($target, $this->suspensionAction($scope));
            $this->assertNotSuspended($target, $feedback);
            if ($feedback->hasErrors()) {
                return $feedback;
            }

            [$startsAt, $endsAt] = $this->resolveDates($dto, $feedback);
            if ($feedback->hasErrors()) {
                return $feedback;
            }

            $suspension = (new AccountSuspension())
                ->setScope($scope)
                ->setUser($target)
                ->setOrganization($organization)
                ->setReason($dto->reason)
                ->setStartsAt($startsAt)
                ->setEndsAt($endsAt)
                ->setCreatedBy($this->securityService->getCurrentUser());

            $this->entityManager->persist($suspension);
            $this->applySuspension($target, $organization);
            $this->entityManager->flush();

            $this->notifyAccountSuspension($target, $suspension);

            $feedback->setData([
                'id' => $target->getId(),
                'scope' => $scope->value,
                'status' => UserStatus::SUSPENDED->value,
                'startsAt' => $startsAt->format('Y-m-d H:i:s'),
                'endsAt' => $endsAt?->format('Y-m-d H:i:s'),
            ])
                ->setFlushDescription('Compte suspendu avec succès. Un email a été envoyé à la personne concernée.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function reactivateUser(string $userId): Feedback
    {
        $feedback = new Feedback();

        try {
            $target = $this->findUser($userId);
            if ($target === null) {
                $feedback->setErrorFlushDescription('Utilisateur introuvable.')->autoInitFlush();
                return $feedback;
            }

            $scope = $this->scopeFor($target);
            $organization = $this->resolveActingOrganization($target, $this->reactivationAction($scope));

            if ($target->getStatus() !== UserStatus::SUSPENDED) {
                $feedback->setErrorFlushDescription('Ce compte n’est pas suspendu.')->autoInitFlush();
                return $feedback;
            }

            $suspensions = $this->suspensionRepository->findActiveForUser($target);
            $now = new \DateTimeImmutable();
            foreach ($suspensions as $suspension) {
                $suspension->setCanceledAt($now);
            }

            $target->setStatus(UserStatus::ACTIVE);
            $this->liftMemberships($target, $organization);
            $this->entityManager->flush();

            foreach ($suspensions as $suspension) {
                $this->suspensionMailer->sendAccountReactivated($target, $suspension);
            }
            $this->notifyInApp($target, 'Compte réactivé', 'Votre compte a été réactivé. Vous pouvez de nouveau vous connecter.');

            $feedback->setData([
                'id' => $target->getId(),
                'status' => UserStatus::ACTIVE->value,
            ])
                ->setFlushDescription('Suspension levée avec succès. Un email de confirmation a été envoyé.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Throwable $e) {
            $feedback->setErrorFlushDescription('Erreur : ' . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    /*
     * ============================================================
     * LEVÉE AUTOMATIQUE (échéance atteinte)
     * ============================================================
     */

    /**
     * @return array{organizations: int, users: int}
     */
    public function processExpired(\DateTimeImmutable $now): array
    {
        $counts = ['organizations' => 0, 'users' => 0];
        $expired = $this->suspensionRepository->findExpired($now);

        foreach ($expired as $suspension) {
            $suspension->setCanceledAt($now);

            if ($suspension->getScope() === SuspensionScope::ORGANIZATION) {
                $organization = $suspension->getOrganization();
                if ($organization !== null && !$organization->isActive()) {
                    $organization->setActive(true);
                    $counts['organizations']++;

                    foreach ($this->getOrganizationRecipients($organization) as $recipient) {
                        $this->suspensionMailer->sendOrganizationReactivated($recipient, $organization, $suspension);
                        $this->notifyInApp($recipient, 'Organisation réactivée', sprintf(
                            'La suspension de l’organisation « %s » est arrivée à échéance : accès rétabli.', $organization->getName()
                        ));
                    }
                }
                continue;
            }

            $user = $suspension->getUser();
            if ($user !== null && $user->getStatus() === UserStatus::SUSPENDED) {
                $user->setStatus(UserStatus::ACTIVE);
                $this->liftMemberships($user, $suspension->getOrganization());
                $counts['users']++;

                $this->suspensionMailer->sendAccountReactivated($user, $suspension);
                $this->notifyInApp($user, 'Compte réactivé', 'La suspension de votre compte est arrivée à échéance : accès rétabli.');
            }
        }

        if ($expired !== []) {
            $this->entityManager->flush();
        }

        return $counts;
    }

    /*
     * ============================================================
     * DATE DE VALIDATION
     * ============================================================
     */

    /**
     * @return array{0: \DateTimeImmutable, 1: ?\DateTimeImmutable}
     */
    private function resolveDates(SuspensionRequestDTO $dto, Feedback $feedback): array
    {
        $now = new \DateTimeImmutable();

        try {
            $startsAt = $dto->startsAt !== null
                ? new \DateTimeImmutable($dto->startsAt)
                : $now;
        } catch (\Throwable) {
            $feedback->setErrorFlushDescription('La date de début de la suspension est invalide.')->autoInitFlush();
            return [$now, null];
        }

        if ($dto->durationDays !== null && $dto->endsAt !== null) {
            $feedback->setErrorFlushDescription('Choisissez une durée en jours OU une date de fin, pas les deux.')->autoInitFlush();
            return [$startsAt, null];
        }

        if ($dto->durationDays !== null && $dto->durationDays < 1) {
            $feedback->setErrorFlushDescription('La durée de la suspension doit être d’au moins 1 jour.')->autoInitFlush();
            return [$startsAt, null];
        }

        if ($dto->endsAt !== null) {
            try {
                $endsAt = new \DateTimeImmutable($dto->endsAt);
            } catch (\Throwable) {
                $feedback->setErrorFlushDescription('La date de fin de la suspension est invalide.')->autoInitFlush();
                return [$startsAt, null];
            }

            if ($endsAt <= $startsAt) {
                $feedback->setErrorFlushDescription('La date de fin doit être postérieure à la date de début.')->autoInitFlush();
                return [$startsAt, null];
            }

            return [$startsAt, $endsAt];
        }

        if ($dto->durationDays !== null) {
            return [$startsAt, $startsAt->modify(sprintf('+%d days', $dto->durationDays))];
        }

        return [$startsAt, null];
    }

    /*
     * ============================================================
     * HELPERS
     * ============================================================
     */

    private function findOrganization(string $id): ?HealthcareOrganization
    {
        return $this->entityManager->getRepository(HealthcareOrganization::class)->find($id);
    }

    private function findUser(string $id): ?User
    {
        return $this->entityManager->getRepository(User::class)->find($id);
    }

    private function scopeFor(User $user): SuspensionScope
    {
        if ($user instanceof Patient) {
            return SuspensionScope::PATIENT;
        }

        if ($user instanceof HealthcareProfessional) {
            return SuspensionScope::PROFESSIONAL;
        }

        throw new AccessDeniedException('Ce compte ne peut pas être suspendu (seuls les professionnels et les patients).');
    }

    private function suspensionAction(SuspensionScope $scope): SecurityAction
    {
        return $scope === SuspensionScope::PATIENT
            ? SecurityAction::SUSPEND_PATIENT
            : SecurityAction::SUSPEND_PROFESSIONAL;
    }

    private function reactivationAction(SuspensionScope $scope): SecurityAction
    {
        return $scope === SuspensionScope::PATIENT
            ? SecurityAction::ACTIVATE_PATIENT
            : SecurityAction::ACTIVATE_USER;
    }

    /**
     * Vérifie les droits de l'administrateur et que la cible appartient bien
     * à son organisation. Pour le ROOT, aucune appartenance n'est exigée.
     */
    private function resolveActingOrganization(User $target, SecurityAction $action): ?HealthcareOrganization
    {
        if ($this->securityService->isSuperAdmin()) {
            return null;
        }

        $currentUser = $this->securityService->getCurrentUser();
        $organization = null;

        foreach ($currentUser->getOrganizationMemberships() as $membership) {
            if ($membership->getStatus()->isActive() && $membership->getOrganization() !== null) {
                $organization = $membership->getOrganization();
                break;
            }
        }

        if ($organization === null) {
            throw new AccessDeniedException('Aucune organisation active pour cet administrateur.');
        }

        $this->securityService->checkOrganizationAccess($organization, $action);

        if (!$this->belongsToOrganizationForSuspension($target, $organization)) {
            throw new AccessDeniedException('Ce compte n’appartient pas à votre organisation.');
        }

        return $organization;
    }

    /**
     * Vérifie que le compte cible est rattaché à l'organisation, que sa
     * membership soit ACTIVE ou SUSPENDUE (nécessaire pour la réactivation).
     */
    private function belongsToOrganizationForSuspension(
        User $target,
        HealthcareOrganization $organization
    ): bool {
        foreach ($target->getOrganizationMemberships() as $membership) {
            if ($membership->getOrganization()?->getId() !== $organization->getId()) {
                continue;
            }

            if (
                $membership->getStatus() === MembershipStatus::ACTIVE ||
                $membership->getStatus() === MembershipStatus::SUSPENDED
            ) {
                return true;
            }
        }

        return false;
    }

    private function assertNotSuspended(User $target, Feedback $feedback): void
    {
        if ($target->getStatus() === UserStatus::SUSPENDED) {
            $feedback->setErrorFlushDescription('Ce compte est déjà suspendu.')->autoInitFlush();
        }
    }

    private function applySuspension(User $target, ?HealthcareOrganization $organization): void
    {
        $target->setStatus(UserStatus::SUSPENDED);

        $this->suspendMemberships($target, $organization);
    }

    private function suspendMemberships(User $target, ?HealthcareOrganization $organization): void
    {
        foreach ($target->getOrganizationMemberships() as $membership) {
            if ($membership->getStatus() !== \App\Entity\Healthcare\MembershipStatus::ACTIVE) {
                continue;
            }

            if ($organization === null || $membership->getOrganization()?->getId() === $organization->getId()) {
                $membership->setStatus(MembershipStatus::SUSPENDED);
            }
        }
    }

    private function liftMemberships(User $target, ?HealthcareOrganization $organization): void
    {
        foreach ($target->getOrganizationMemberships() as $membership) {
            if ($membership->getStatus() !== MembershipStatus::SUSPENDED) {
                continue;
            }

            if ($organization === null || $membership->getOrganization()?->getId() === $organization->getId()) {
                $membership->setStatus(MembershipStatus::ACTIVE);
            }
        }
    }

    /**
     * @return list<User>
     */
    private function getOrganizationRecipients(HealthcareOrganization $organization): array
    {
        $recipients = [];
        foreach ($this->membershipRepository->findActiveUsersByOrganization($organization) as $user) {
            $recipients[$user->getId()] = $user;
        }
        foreach ($this->membershipRepository->findActiveAdminsByOrganization($organization) as $user) {
            $recipients[$user->getId()] = $user;
        }

        return array_values($recipients);
    }

    private function notifyOrganizationSuspension(
        User $recipient,
        HealthcareOrganization $organization,
        AccountSuspension $suspension
    ): void {
        $this->suspensionMailer->sendOrganizationSuspended($recipient, $organization, $suspension);

        $this->notifyInApp($recipient, 'Organisation suspendue', sprintf(
            'L’organisation « %s » a été suspendue.%s',
            $organization->getName(),
            $suspension->getEndsAt()
                ? ' Accès rétabli le ' . $suspension->getEndsAt()->format('d/m/Y') . '.'
                : ''
        ));
    }

    private function notifyAccountSuspension(User $target, AccountSuspension $suspension): void
    {
        $this->suspensionMailer->sendAccountSuspended($target, $suspension);

        $this->notifyInApp($target, 'Compte suspendu', sprintf(
            'Votre compte a été suspendu.%s Motif : %s',
            $suspension->getEndsAt()
                ? ' Accès rétabli le ' . $suspension->getEndsAt()->format('d/m/Y') . '.'
                : ' Accès rétabli uniquement après décision de votre organisation.',
            (string) $suspension->getReason()
        ));
    }

    private function notifyInApp(User $user, string $title, string $body): void
    {
        $this->notificationService->createDirect(
            $user,
            NotificationType::SYSTEM_ALERT,
            $title,
            $body,
            ReminderChannel::IN_APP,
            'AccountSuspension'
        );
    }
}