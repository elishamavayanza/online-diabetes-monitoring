<?php

namespace App\Service\Communication;

use App\Entity\Healthcare\ExternalFollowInvitation;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Twig\Environment;

/**
 * Envoie les courriels liés aux invitations de suivi externe (Mailpit en dev).
 */
class ExternalFollowMailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
        private readonly RequestStack $requestStack,
        private readonly ParameterBagInterface $params
    ) {}

    public function sendInvitation(ExternalFollowInvitation $invitation): void
    {
        $this->send(
            'emails/external_follow_invitation.html.twig',
            'Invitation à suivre un patient — OnlineDIAB',
            (string) $invitation->getEmail(),
            [
                'invitation' => $invitation,
                'acceptUrl' => $this->buildAcceptUrl($invitation),
            ]
        );
    }

    public function sendRenewal(ExternalFollowInvitation $invitation): void
    {
        $this->send(
            'emails/external_follow_renewed.html.twig',
            'Votre accès à un patient a été prolongé — OnlineDIAB',
            (string) $invitation->getEmail(),
            [
                'invitation' => $invitation,
                'acceptUrl' => $this->buildAcceptUrl($invitation),
            ]
        );
    }

    public function sendRevoked(ExternalFollowInvitation $invitation): void
    {
        $this->send(
            'emails/external_follow_revoked.html.twig',
            'Votre accès à un patient a été retiré — OnlineDIAB',
            (string) $invitation->getEmail(),
            [
                'invitation' => $invitation,
            ]
        );
    }

    public function sendAcceptedConfirmation(ExternalFollowInvitation $invitation): void
    {
        $this->send(
            'emails/external_follow_accepted.html.twig',
            'Invitation acceptée — votre suivi commence',
            (string) $invitation->getEmail(),
            [
                'invitation' => $invitation,
            ]
        );
    }

    /**
     * Notifie les administrateurs de l'organisation d'origine que le
     * professionnel externe a fermé lui-même son suivi (motif inclus).
     *
     * @param list<string> $adminEmails
     */
    public function sendSelfClosed(ExternalFollowInvitation $invitation, array $adminEmails): void
    {
        foreach (array_values($adminEmails) as $adminEmail) {
            try {
                $this->send(
                    'emails/external_follow_self_closed.html.twig',
                    'Un professionnel a fermé son suivi externe — OnlineDIAB',
                    $adminEmail,
                    [
                        'invitation' => $invitation,
                    ]
                );
            } catch (\Throwable) {
                // L'échec d'un destinataire ne bloque pas les autres.
            }
        }
    }

    private function buildAcceptUrl(ExternalFollowInvitation $invitation): string
    {
        $request = $this->requestStack->getCurrentRequest();

        $host = $request !== null
            ? $request->getSchemeAndHttpHost()
            : 'http://localhost';

        return $host . '/invite/' . $invitation->getToken();
    }

    private function send(
        string $template,
        string $subject,
        string $to,
        array $context
    ): void {
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