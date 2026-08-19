<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'General', description: 'Points de terminaison généraux de l’API')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    #[OA\Get(
        description: 'Affiche la page d\'accueil de l\'application DiabCare.',
        summary: 'Page d\'accueil'
    )]
    #[OA\Response(
        response: 200,
        description: 'Page HTML de l\'application',
        content: new OA\MediaType(mediaType: 'text/html')
    )]
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }
}
