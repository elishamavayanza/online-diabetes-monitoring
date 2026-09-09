<?php

namespace App\Command;

use App\Entity\Appointment\ReminderChannel;
use App\Entity\Notification\NotificationType;
use App\Entity\Notification\ReminderRule;
use App\Entity\Notification\ReminderTargetType;
use App\Repository\Appointment\AppointmentReminderRepository;
use App\Repository\Notification\ReminderRuleRepository;
use App\Service\Notification\CronExpression;
use App\Service\Notification\NotificationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Envoie les notifications de rappel en attente :
 *  - les AppointmentReminder dont scheduledFor est atteinte ;
 *  - les ReminderRule dont l'expression CRON correspond à l'instant courant.
 *
 * À planifier toutes les minutes, par exemple :
 *   * * * * * php bin/console app:notifications:send-pending
 */
#[AsCommand(
    name: 'app:notifications:send-pending',
    description: 'Envoie les rappels programmés (rendez-vous et règles CRON) via notification + email.'
)]
class SendPendingNotificationsCommand extends Command
{
    public function __construct(
        private readonly AppointmentReminderRepository $appointmentReminderRepository,
        private readonly ReminderRuleRepository $reminderRuleRepository,
        private readonly NotificationService $notificationService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $now = new \DateTimeImmutable();

        $appointmentSent = $this->processAppointmentReminders($io, $now);
        $ruleSent = $this->processReminderRules($io, $now);

        $io->success(sprintf(
            'Terminé : %d rappel(s) de rendez-vous et %d règle(s) de rappel traité(s).',
            $appointmentSent,
            $ruleSent
        ));

        return Command::SUCCESS;
    }

    private function processAppointmentReminders(SymfonyStyle $io, \DateTimeImmutable $now): int
    {
        $count = 0;
        $pending = $this->appointmentReminderRepository->findPendingReminders($now);

        foreach ($pending as $reminder) {
            $appointment = $reminder->getAppointment();
            $patient = $appointment?->getPatient();
            if (!$patient) {
                continue;
            }

            $scheduledAt = $appointment->getScheduledAt();
            $dateLabel = $scheduledAt?->format('d/m/Y \à H\hi');

            $this->notificationService->createDirect(
                $patient,
                NotificationType::APPOINTMENT_REMINDER,
                'Rappel de rendez-vous',
                $dateLabel
                    ? sprintf('Vous avez un rendez-vous prévu le %s. N\'oubliez pas votre rendez-vous !', $dateLabel)
                    : 'Vous avez un rendez-vous prévu prochainement. N\'oubliez pas !',
                self::effectiveChannel($reminder->getChannel()),
                'Appointment',
                (string) $appointment->getId()
            );

            $reminder->setSentAt($now);
            $count++;
        }

        return $count;
    }

    private function processReminderRules(SymfonyStyle $io, \DateTimeImmutable $now): int
    {
        $count = 0;
        $rules = $this->reminderRuleRepository->findAllActive();

        foreach ($rules as $rule) {
            if (!$this->isDue($rule, $now)) {
                continue;
            }

            $patient = $rule->getPatient();
            if (!$patient) {
                continue;
            }

            [$type, $title, $body] = $this->buildForTarget($rule->getTargetType());

            $this->notificationService->createDirect(
                $patient,
                $type,
                $title,
                $body,
                ReminderChannel::EMAIL,
                'ReminderRule',
                (string) $rule->getId()
            );

            $rule->setLastTriggeredAt($now);
            $count++;
        }

        return $count;
    }

    private function isDue(ReminderRule $rule, \DateTimeImmutable $now): bool
    {
        $expression = $rule->getCronExpression();
        if (!$expression || !CronExpression::isDue($expression, $now)) {
            return false;
        }

        // Anti-doublon : on ne retrigge pas une règle déjà exécutée dans la même minute.
        $last = $rule->getLastTriggeredAt();
        if ($last !== null && $last->format('Y-m-d H:i') === $now->format('Y-m-d H:i')) {
            return false;
        }

        return true;
    }

    /**
     * @return array{0: NotificationType, 1: string, 2: string}
     */
    private function buildForTarget(?ReminderTargetType $targetType): array
    {
        return match ($targetType) {
            ReminderTargetType::MEDICATION => [
                NotificationType::MEDICATION_REMINDER,
                'Rappel de médicament',
                'C\'est l\'heure de prendre vos médicaments, comme prévu dans votre traitement.',
            ],
            ReminderTargetType::APPOINTMENT => [
                NotificationType::APPOINTMENT_REMINDER,
                'Rappel de rendez-vous',
                'Vous avez un rendez-vous à ne pas manquer. Pensez à prévenir votre professionnel en cas d\'impossibilité.',
            ],
            default => [
                NotificationType::MEASUREMENT_REMINDER,
                'Rappel de mesure',
                'Pensez à enregistrer votre mesure (glycémie, tension artérielle, poids…) pour suivre votre santé.',
            ],
        };
    }

    /**
     * SMS et PUSH ne sont pas encore implémentés : on redirige ces canaux
     * vers l'email (Mailpit en dev). IN_APP reste uniquement in-app.
     */
    private static function effectiveChannel(?ReminderChannel $channel): ReminderChannel
    {
        if ($channel === ReminderChannel::IN_APP) {
            return ReminderChannel::IN_APP;
        }

        return ReminderChannel::EMAIL;
    }
}