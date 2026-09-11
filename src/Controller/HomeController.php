<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use App\Service\System\SystemSettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'General', description: 'Points de terminaison généraux de l’API')]
class HomeController extends AbstractController
{
    public function __construct(
        private readonly SystemSettingsService $settingsService
    ) {
    }

    #[Route('/', name: 'app_home', methods: ['GET'])]
    #[Route(
        '/{reactRouting}',
        name: 'app_spa',
        requirements: ['reactRouting' => '^(?!api|_profiler|_wdt|build|reset-password).+'],
        methods: ['GET']
    )]
    #[OA\Get(
        description: 'Affiche la page d\'accueil de l\'application.',
        summary: 'Page d\'accueil'
    )]
    #[OA\Response(
        response: 200,
        description: 'Page HTML de l\'application',
        content: new OA\MediaType(mediaType: 'text/html')
    )]
    public function index(?string $reactRouting = null): Response
    {
        $settings = $this->settingsService->getSingleton();

        return $this->render('base.html.twig', [
            'systemName' => $settings->getSystemName() ?: 'OnlineDIAB',
            'logoUrl' => $settings->getLogoUrl(),
        ]);
    }
}
