<?php

namespace App\Controller\Api;

use App\Entity\Identity\User;
use App\Service\Security\PasswordManager;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Response;

#[OA\Tag(name: 'Authentication', description: 'Gestion de l\'authentification et du profil utilisateur')]
class AuthController extends AbstractController
{
    #[Route('/api/login_check', name: 'api_login_check', methods: ['POST'])]
    #[OA\Post(
        summary: 'Connexion utilisateur et obtention du token JWT',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'username', type: 'string', example: 'root@diabcare.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'Test@123'),
                    new OA\Property(property: 'remember_me', description: 'Générer un token longue durée', type: 'boolean', example: false)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Connexion réussie',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'token', type: 'string'),
                        new OA\Property(property: 'fullName', type: 'string'),
                        new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string'))
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Identifiants invalides')
        ]
    )]
    public function login(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['username'] ?? $data['email'] ?? '';
        $password = $data['password'] ?? '';

        /** @var User|null $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['message' => 'Identifiants invalides.'], 401);
        }

        // 1. Vérifier si le compte est bloqué suite à 5 tentatives échouées
        if ($user->getLockedUntil() && $user->getLockedUntil() > new \DateTimeImmutable()) {
            $remainingMinutes = ceil(($user->getLockedUntil()->getTimestamp() - time()) / 60);
            return new JsonResponse([
                'message' => sprintf('Compte temporairement bloqué suite à trop de tentatives infructueuses. Réessayez dans %d minute(s).', $remainingMinutes)
            ], 429);
        }

        // 2. Vérifier le mot de passe
        if (!$passwordHasher->isPasswordValid($user, $password)) {
            $attempts = $user->getLoginAttempts() + 1;
            $user->setLoginAttempts($attempts);

            if ($attempts >= 5) {
                // Bloquer pour 15 minutes
                $user->setLockedUntil(new \DateTimeImmutable('+15 minutes'));
                $entityManager->flush();

                return new JsonResponse([
                    'message' => 'Compte bloqué pour 15 minutes suite à 5 tentatives infructueuses.'
                ], 429);
            }

            $entityManager->flush();

            return new JsonResponse([
                'message' => sprintf('Identifiants invalides. Tentative %d/5.', $attempts)
            ], 401);
        }

        // 3. Connexion réussie : Réinitialiser les compteurs et enregistrer la dernière connexion
        $user->setLoginAttempts(0);
        $user->setLockedUntil(null);
        $user->setLastLoginAt(new \DateTimeImmutable());
        $entityManager->flush();

        // 4. Génération du token JWT
        $token = $jwtManager->create($user);
        $refreshToken = $this->issueRefreshToken($user);
        $entityManager->flush();

        return new JsonResponse([
            'token' => $token,
            'refresh_token' => $refreshToken,
            'fullName' => $user->getFullName(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route('/api/forgot-password', name: 'api_forgot_password', methods: ['POST'])]
    #[OA\Post(
        summary: 'Demander un e-mail de réinitialisation de mot de passe',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'email', type: 'string', example: 'root@diabcare.com')
            ])
        ),
        responses: [
            new OA\Response(response: 200, description: 'Email de réinitialisation envoyé')
        ]
    )]
    public function forgotPassword(
        Request $request,
        EntityManagerInterface $entityManager,
        PasswordManager $passwordManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';

        /** @var User|null $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        // Par sécurité, on renvoie toujours un succès pour éviter l'énumération des e-mails
        if ($user) {
            $passwordManager->sendResetToken($user);
        }

        return new JsonResponse(['message' => 'Si cet e-mail existe, un lien de réinitialisation a été envoyé.']);
    }

    #[Route('/api/reset-password', name: 'api_reset_password', methods: ['POST'])]
    #[OA\Post(
        summary: 'Réinitialiser le mot de passe via le token reçu par e-mail',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'token', type: 'string'),
                new OA\Property(property: 'newPassword', type: 'string')
            ])
        ),
        responses: [
            new OA\Response(response: 200, description: 'Mot de passe réinitialisé avec succès'),
            new OA\Response(response: 400, description: 'Jeton invalide ou expiré')
        ]
    )]
    public function resetPassword(
        Request $request,
        PasswordManager $passwordManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? '';
        $newPassword = $data['newPassword'] ?? '';

        try {
            $passwordManager->resetPasswordWithToken($token, $newPassword);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue.'], 500);
        }

        return new JsonResponse(['message' => 'Mot de passe réinitialisé avec succès.']);
    }

    #[Route('/api/change-password', name: 'api_change_password', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[OA\Post(
        summary: 'Modifier volontairement son mot de passe (Utilisateur connecté)',
        security: [['Bearer' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'oldPassword', type: 'string'),
                    new OA\Property(property: 'newPassword', type: 'string'),
                    new OA\Property(property: 'confirmPassword', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Mot de passe mis à jour'),
            new OA\Response(response: 400, description: 'Erreur de validation')
        ]
    )]
    public function changePassword(
        Request $request,
        PasswordManager $passwordManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $oldPassword = $data['oldPassword'] ?? '';
        $newPassword = $data['newPassword'] ?? '';
        $confirmPassword = $data['confirmPassword'] ?? '';

        // 1. Vérification de la correspondance des mots de passe
        if ($newPassword !== $confirmPassword) {
            return new JsonResponse(['message' => 'Le nouveau mot de passe et la confirmation ne correspondent pas.'], 400);
        }

        try {
            // 2. Appel au service pour vérifier l'ancien mot de passe et enregistrer le nouveau
            $passwordManager->updatePassword(
                $this->getUser(),
                $oldPassword,
                $newPassword
            );
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue.'], 500);
        }

        return new JsonResponse(['message' => 'Mot de passe mis à jour avec succès.']);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password_form', methods: ['GET', 'POST'])]
    public function resetPasswordForm(
        Request $request,
        string $token,
        PasswordManager $passwordManager
    ): Response {
        // Si c'est une soumission POST, on traite
        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('newPassword');
            $confirmPassword = $request->request->get('confirmPassword');

            if ($newPassword !== $confirmPassword) {
                return $this->render('security/reset_password_form.html.twig', [
                    'token' => $token,
                    'error' => 'Les mots de passe ne correspondent pas.',
                    'success' => null,
                ]);
            }

            try {
                $passwordManager->resetPasswordWithToken($token, $newPassword);
                return $this->render('security/reset_password_form.html.twig', [
                    'token' => $token,
                    'success' => 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.',
                    'error' => null,
                ]);
            } catch (\InvalidArgumentException $e) {
                return $this->render('security/reset_password_form.html.twig', [
                    'token' => $token,
                    'error' => $e->getMessage(),
                    'success' => null,
                ]);
            } catch (\Exception $e) {
                return $this->render('security/reset_password_form.html.twig', [
                    'token' => $token,
                    'error' => 'Une erreur est survenue. Veuillez réessayer.',
                    'success' => null,
                ]);
            }
        }

        // GET : afficher le formulaire
        return $this->render('security/reset_password_form.html.twig', [
            'token' => $token,
            'error' => null,
            'success' => null,
        ]);

    }

    #[Route('/api/token/refresh', name: 'api_token_refresh', methods: ['POST'])]
    #[OA\Post(
        summary: 'Rafraîchir le token JWT sans ré-authentification',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'refresh_token', type: 'string', example: 'votre_refresh_token')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Nouveau token généré avec succès'),
            new OA\Response(response: 401, description: 'Refresh token invalide ou expiré')
        ]
    )]
    public function refreshToken(Request $request, EntityManagerInterface $entityManager, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $refreshTokenString = $data['refresh_token'] ?? '';
        if (!is_string($refreshTokenString) || $refreshTokenString === '') {
            return new JsonResponse(['message' => 'Refresh token manquant.'], Response::HTTP_UNAUTHORIZED);
        }

        /** @var User|null $user */
        $user = $entityManager->getRepository(User::class)->findOneBy([
            'refreshTokenHash' => hash('sha256', $refreshTokenString),
        ]);

        if ($user === null || $user->getRefreshTokenExpiresAt() === null || $user->getRefreshTokenExpiresAt() <= new \DateTimeImmutable()) {
            return new JsonResponse(['message' => 'Session expirée.'], Response::HTTP_UNAUTHORIZED);
        }

        // Rotation : le refresh token présenté ne peut plus être réutilisé.
        $newRefreshToken = $this->issueRefreshToken($user);
        $entityManager->flush();

        return new JsonResponse([
            'token' => $jwtManager->create($user),
            'refresh_token' => $newRefreshToken,
        ]);
    }

    private function issueRefreshToken(User $user): string
    {
        $token = bin2hex(random_bytes(48));
        $user->setRefreshTokenHash(hash('sha256', $token));
        $user->setRefreshTokenExpiresAt(new \DateTimeImmutable('+14 days'));

        return $token;
    }
}
