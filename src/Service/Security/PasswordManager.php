<?php

namespace App\Service\Security;

use App\Entity\Identity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class PasswordManager
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
        private Environment $twig,
        private MailerInterface $mailer,
        private UrlGeneratorInterface $router,
        private ParameterBagInterface $params

    ) {}

    // CAS 1 : Changement volontaire (Besoin de l'ancien mot de passe)
    public function changePassword(User $user, string $oldPassword, string $newPassword): void
    {
        if (!$this->passwordHasher->isPasswordValid($user, $oldPassword)) {
            throw new \InvalidArgumentException('Ancien mot de passe incorrect.');
        }

        $this->applyNewPassword($user, $newPassword);
    }

    // CAS 2 : Réinitialisation via Token (Sans ancien mot de passe)
    public function resetPasswordWithToken(string $token, string $newPassword): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if (!$user || $user->getResetTokenExpiresAt() < new \DateTimeImmutable()) {
            throw new \InvalidArgumentException('Jeton invalide ou expiré.');
        }

        $this->applyNewPassword($user, $newPassword);

        $user->setResetToken(null);
        $user->setResetTokenExpiresAt(null);
        $this->entityManager->flush();
    }

    public function sendResetToken(User $user): void
    {
        $token = bin2hex(random_bytes(32));
        $user->setResetToken($token);
        $user->setResetTokenExpiresAt(new \DateTimeImmutable('+1 hour'));
        $this->entityManager->flush();

        $resetUrl = $this->router->generate('app_reset_password_form', [
            'token' => $token
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $logoPath = $this->params->get('kernel.project_dir') . '/public/images/logo.png';

        // 1. Créer l'objet Email
        $email = (new Email())
            ->from('no-reply@diabcare.com')
            ->to($user->getEmail())
            ->subject('Réinitialisation de votre mot de passe');

        // 2. Attacher l'image et récupérer l'objet Attachement pour obtenir son CID
        $logoPart = \Symfony\Component\Mime\Part\DataPart::fromPath($logoPath);
        $logoCid = $logoPart->getContentId();
        $email->addPart($logoPart);

        // 3. Rendre le template Twig en passant le bon CID (sous forme de chaîne, ex: "cid:...")
        $emailHtml = $this->twig->render('emails/reset_password.html.twig', [
            'resetUrl' => $resetUrl,
            'user' => $user,
            'logo_cid' => 'cid:' . $logoCid,
        ]);

        // 4. Assigner le HTML et envoyer
        $email->html($emailHtml);
        $this->mailer->send($email);
    }

    public function updatePassword(?\Symfony\Component\Security\Core\User\UserInterface $user, mixed $oldPassword, mixed $newPassword): void
    {
        // 1. Vérifier si l'utilisateur est bien connecté
        if (!$user instanceof User) {
            throw new \InvalidArgumentException('Utilisateur non authentifié.');
        }

        // 2. S'assurer que les mots de passe sont bien des chaînes de caractères (string)
        if (!is_string($oldPassword) || !is_string($newPassword)) {
            throw new \InvalidArgumentException('Les mots de passe doivent être des chaînes de caractères.');
        }

        // 3. Vérification de l'ancien mot de passe
        if (!$this->passwordHasher->isPasswordValid($user, $oldPassword)) {
            throw new \InvalidArgumentException('Ancien mot de passe incorrect.');
        }

        // 4. Hashage et mise à jour via votre méthode privée existante
        $this->applyNewPassword($user, $newPassword);
    }
    private function applyNewPassword(User $user, string $newPassword): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
        $this->entityManager->flush();
    }
}
