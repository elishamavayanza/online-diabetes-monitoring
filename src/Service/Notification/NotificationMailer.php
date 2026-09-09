<?php

namespace App\Service\Notification;

use App\Entity\Notification\Notification;
use App\Entity\Appointment\ReminderChannel;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Twig\Environment;

/**
 * Envoie les courriels de notification (Mailpit en dev).
 * Utilisé dès que le canal d'une notification est EMAIL.
 */
class NotificationMailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
        private readonly RequestStack $requestStack,
        private readonly ParameterBagInterface $params
    ) {}

    /**
     * Envoie l'email correspondant à une notification persistée.
     * L'échec est journalisé et ne bloque jamais le reste du traitement.
     */
    public function send(Notification $notification): void
    {
        $user = $notification->getUser();
        $email = $user?->getEmail();
        if (!$email || $notification->getChannel() !== ReminderChannel::EMAIL) {
            return;
        }

        try {
            $this->sendEmail(
                'emails/notification.html.twig',
                $this->buildSubject($notification),
                $email,
                [
                    'notification' => $notification,
                    'appUrl' => $this->buildAppUrl($notification),
                ]
            );
        } catch (\Throwable $e) {
            try {
                $logger = $this->params->get('kernel.logger');
                if (method_exists($logger, 'error')) {
                    $logger->error(sprintf('Échec envoi email notification %s : %s', $notification->getId(), $e->getMessage()));
                }
            } catch (\Throwable) {
                // aucun log disponible : silencieux
            }
        }
    }

    private function buildSubject(Notification $notification): string
    {
        $type = $notification->getType()?->value;
        $label = match ($type) {
            'MEDICATION_REMINDER' => 'Rappel de médicament',
            'APPOINTMENT_REMINDER' => 'Rappel de rendez-vous',
            'MEASUREMENT_REMINDER' => 'Rappel de mesure',
            'MESSAGE_RECEIVED' => 'Nouveau message',
            'PRESCRIPTION_UPDATED' => 'Ordonnance mise à jour',
            default => 'Notification OnlineDIAB',
        };

        return $label . ' — ' . (string) $notification->getTitle();
    }

    private function buildAppUrl(Notification $notification): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $host = $request !== null
            ? $request->getSchemeAndHttpHost()
            : 'http://localhost';

        // Les patients voient leurs notifications côté /patient/notifications.
        $role = $notification->getUser()?->getRoles()[0] ?? null;
        $path = match ($role) {
            'ROLE_PATIENT' => '/patient/notifications',
            'ROLE_ADMIN' => '/admin/notifications',
            'ROLE_CLINICIAN' => '/clinician/notifications',
            'ROLE_NUTRITIONIST' => '/nutritionist/notifications',
            default => '/',
        };

        return $host . $path;
    }

    private function sendEmail(string $template, string $subject, string $to, array $context): void
    {
        $logoPath = $this->params->get('kernel.project_dir') . '/public/images/logo.png';

        $logoPart = DataPart::fromPath($logoPath);
        $logoCid = $logoPart->getContentId();

        $email = (new Email())
            ->from('no-reply@diabcare.com')
            ->to($to)
            ->subject($subject)
            ->addPart($logoPart);

        $context['logo_cid'] = 'cid:' . $logoCid;

        $emailHtml = $this->twig->render($template, $context);
        $email->html($emailHtml);

        $this->mailer->send($email);
    }
}