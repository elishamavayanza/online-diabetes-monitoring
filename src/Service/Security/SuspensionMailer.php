<?php

namespace App\Service\Security;

use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\User;
use App\Entity\Security\AccountSuspension;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Twig\Environment;

/**
 * Emails liés aux suspensions de comptes et d'organisations (Mailpit en dev).
 * Un échec d'envoi ne bloque jamais le durcissement du compte.
 */
class SuspensionMailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
        private readonly RequestStack $requestStack,
        private readonly ParameterBagInterface $params
    ) {}

    public function sendAccountSuspended(User $user, AccountSuspension $suspension): void
    {
        $this->send(
            'emails/suspension_account.html.twig',
            'Votre compte a été suspendu — OnlineDIAB',
            (string) $user->getEmail(),
            $this->accountContext($user, $suspension, 'suspended')
        );
    }

    public function sendAccountReactivated(User $user, AccountSuspension $suspension): void
    {
        $this->send(
            'emails/suspension_account.html.twig',
            'Votre compte a été réactivé — OnlineDIAB',
            (string) $user->getEmail(),
            $this->accountContext($user, $suspension, 'reactivated')
        );
    }

    public function sendOrganizationSuspended(
        User $user,
        HealthcareOrganization $organization,
        AccountSuspension $suspension
    ): void {
        $this->send(
            'emails/suspension_organization.html.twig',
            'Votre organisation a été suspendue — OnlineDIAB',
            (string) $user->getEmail(),
            $this->organizationContext($user, $organization, $suspension, 'suspended')
        );
    }

    public function sendOrganizationReactivated(
        User $user,
        HealthcareOrganization $organization,
        AccountSuspension $suspension
    ): void {
        if (!$user->getEmail()) {
            return;
        }

        $this->send(
            'emails/suspension_organization.html.twig',
            'Votre organisation a été réactivée — OnlineDIAB',
            (string) $user->getEmail(),
            $this->organizationContext($user, $organization, $suspension, 'reactivated')
        );
    }

    private function accountContext(
        User $user,
        AccountSuspension $suspension,
        string $action
    ): array {
        return [
            'user' => $user,
            'action' => $action,
            'reason' => $suspension->getReason(),
            'endsAt' => $suspension->getEndsAt(),
            'appUrl' => $this->buildAppUrl($user),
        ];
    }

    private function organizationContext(
        User $user,
        HealthcareOrganization $organization,
        AccountSuspension $suspension,
        string $action
    ): array {
        return [
            'user' => $user,
            'action' => $action,
            'organization' => $organization,
            'reason' => $suspension->getReason(),
            'endsAt' => $suspension->getEndsAt(),
            'appUrl' => $this->buildAppUrl($user),
        ];
    }

    private function buildAppUrl(User $user): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $host = $request !== null
            ? $request->getSchemeAndHttpHost()
            : 'http://localhost';

        $role = $user->getRoles()[0] ?? null;
        $path = match ($role) {
            'ROLE_PATIENT' => '/patient',
            'ROLE_ADMIN' => '/admin',
            'ROLE_CLINICIAN' => '/clinician',
            'ROLE_NUTRITIONIST' => '/nutritionist',
            default => '/',
        };

        return $host . $path;
    }

    private function send(string $template, string $subject, string $to, array $context): void
    {
        if ($to === '') {
            return;
        }

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