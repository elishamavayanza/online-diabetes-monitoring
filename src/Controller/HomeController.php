<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'General', description: 'Points de terminaison généraux de l’API')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    #[OA\Get(
        summary: 'Accueil de l’API',
        description: 'Renvoie un message de bienvenue, la version actuelle de l’API ainsi que le lien vers la documentation.'
    )]
    #[OA\Response(
        response: 200,
        description: 'Informations de base de l’API',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Bienvenue sur l\'API DiabCare'),
                new OA\Property(property: 'version', type: 'string', example: 'v1'),
                new OA\Property(property: 'documentation', type: 'string', example: '/api/doc')
            ],
            type: 'object'
        )
    )]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Bienvenue sur l\'API DiabCare',
            'version' => 'v1',
            'documentation' => '/api/doc'
        ]);
    }
}
