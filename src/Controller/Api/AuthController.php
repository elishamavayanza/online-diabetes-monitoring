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
                    new OA\Property(property: 'remember_me', type: 'boolean', description: 'Générer un token longue durée', example: false)
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
        $rememberMe = $data['remember_me'] ?? false;

        /** @var User|null $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['message' => 'Identifiants invalides.'], 401);
        }

        // Génération du token JWT
        $token = $jwtManager->create($user);

        return new JsonResponse([
            'token' => $token,
            'fullName' => $user->getFullName(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route('/api/forgot-password', name: 'api_forgot_password', methods: ['POST'])]
    #[OA\Post(
        summary: 'Demander une réinitialisation de mot de passe',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'email', type: 'string', example: 'root@diabcare.com')
            ])
        ),
        responses: [
            new OA\Response(response: 200, description: 'Email de réinitialisation envoyé')
        ]
    )]
    public function forgotPassword(): JsonResponse
    {
        return new JsonResponse(['message' => 'Instructions envoyées par email.']);
    }

    #[Route('/api/change-password', name: 'api_change_password', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[OA\Post(
        summary: 'Modifier le mot de passe',
        security: [['Bearer' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'oldPassword', type: 'string'),
                    new OA\Property(property: 'newPassword', type: 'string')
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

        try {
            $passwordManager->updatePassword(
                $this->getUser(),
                $data['oldPassword'] ?? '',
                $data['newPassword'] ?? ''
            );
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue.'], 500);
        }

        return new JsonResponse(['message' => 'Mot de passe mis à jour avec succès']);
    }
}
