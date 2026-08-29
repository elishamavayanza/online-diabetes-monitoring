<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Request\Healthcare\HealthcareOrganizationRequestDTO;
use App\DTO\Response\Healthcare\HealthcareOrganizationResponseDTO;
use App\Service\Healthcare\HealthcareOrganizationService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/healthcare-organizations')]
#[OA\Tag(name: 'Healthcare - Organizations', description: 'Gestion des organisations et réseaux de santé')]
class HealthcareOrganizationController extends AbstractController
{
    public function __construct(
        private readonly HealthcareOrganizationService $service
    ) {}

    #[Route('', name: 'api_healthcare_organizations_list', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet de récupérer la liste paginée de toutes les organisations de santé.',
        summary: 'Lister les organisations de santé avec pagination'
    )]
    #[OA\Parameter(
        name: 'page',
        description: 'Numéro de la page (par défaut 1)',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 1)
    )]
    #[OA\Parameter(
        name: 'limit',
        description: "Nombre d'éléments par page (par défaut 10)",
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 10)
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste récupérée avec succès'
    )]
    public function list(\Symfony\Component\HttpFoundation\Request $request): JsonResponse
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(1, min(100, $request->query->getInt('limit', 10)));

        $feedback = $this->service->getPaginated($page, $limit);
        return $this->json($feedback, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'api_healthcare_organizations_show', methods: ['GET'])]
    #[OA\Get(
        description: "Permet de récupérer les détails d'une organisation de santé via son identifiant.",
        summary: "Afficher une organisation de santé"
    )]
    #[OA\Response(
        response: 200,
        description: 'Organisation trouvée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Organisation récupérée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: HealthcareOrganizationResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Organisation introuvable'
    )]
    public function show(string $id): JsonResponse
    {
        $feedback = $this->service->getById($id);
        $status = $feedback->hasErrors() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_healthcare_organizations_create', methods: ['POST'])]
    #[OA\Post(
        description: 'Permet d’enregistrer une nouvelle entité ou structure organisationnelle de santé (avec upload de logo).',
        summary: 'Créer une organisation de santé'
    )]
    #[OA\RequestBody(
        description: 'Paramètres de l’organisation de santé et fichier logo',
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'DiabCare Health Group'),
                    new OA\Property(property: 'shortName', type: 'string', example: 'DHG'),
                    new OA\Property(property: 'type', type: 'string', example: 'NETWORK'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'contact@diabcare.com'),
                    new OA\Property(property: 'phone', type: 'string', example: '+243990000000'),
                    new OA\Property(property: 'website', type: 'string', format: 'uri', example: 'https://www.diabcare.com'),
                    new OA\Property(property: 'logoFile', type: 'string', format: 'binary', description: 'Fichier image du logo'),
                    new OA\Property(property: 'active', type: 'boolean', example: true),
                ]
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Organisation de santé créée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 201),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Organisation de santé créée avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: HealthcareOrganizationResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function create(\Symfony\Component\HttpFoundation\Request $request): JsonResponse
    {
        // Récupération des données textuelles et du fichier depuis la requête HTTP
        $name = $request->request->get('name');
        $shortName = $request->request->get('shortName');
        $type = $request->request->get('type');
        $email = $request->request->get('email');
        $phone = $request->request->get('phone');
        $website = $request->request->get('website');
        $active = filter_var($request->request->get('active', true), FILTER_VALIDATE_BOOLEAN);
        $address = $request->request->all('address'); // Si l'adresse est envoyée sous forme de tableau associatif

        /** @var UploadedFile|null $logoFile */
        $logoFile = $request->files->get('logoFile');

        // Création manuelle du DTO ou instanciation
        $dto = new HealthcareOrganizationRequestDTO(
            name: $name,
            shortName: $shortName,
            type: $type,
            email: $email,
            phone: $phone,
            website: $website,
            logoFile: $logoFile,
            address: !empty($address) ? $address : null,
            active: $active
        );

        // Appel du service
        $feedback = $this->service->create($dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_healthcare_organizations_update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        description: 'Permet de mettre à jour toutes les informations d’une organisation existante.',
        summary: 'Modifier entièrement une organisation de santé (PUT)'
    )]
    #[OA\Patch(
        description: 'Permet de mettre à jour partiellement les informations d’une organisation existante.',
        summary: 'Modifier partiellement une organisation de santé (PATCH)'
    )]
    #[OA\RequestBody(
        description: 'Paramètres mis à jour de l’organisation de santé et nouveau fichier logo optionnel',
        required: false,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'DiabCare Health Group'),
                    new OA\Property(property: 'shortName', type: 'string', example: 'DHG'),
                    new OA\Property(property: 'type', type: 'string', example: 'NETWORK'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'contact@diabcare.com'),
                    new OA\Property(property: 'phone', type: 'string', example: '+243990000000'),
                    new OA\Property(property: 'website', type: 'string', format: 'uri', example: 'https://www.diabcare.com'),
                    new OA\Property(property: 'logoFile', description: 'Nouveau fichier logo optionnel', type: 'string', format: 'binary'),
                    new OA\Property(property: 'active', type: 'boolean', example: true),
                ]
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Organisation mise à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string', example: 'Organisation de santé mise à jour avec succès.'),
                new OA\Property(property: 'data', ref: new Model(type: HealthcareOrganizationResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données de la requête invalides')]
    #[OA\Response(response: 404, description: 'Organisation introuvable')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    public function update(string $id, \Symfony\Component\HttpFoundation\Request $request): JsonResponse
    {
        // Récupération des données textuelles (en gérant le fait qu'en PATCH, certains champs peuvent être absents)
        $name = $request->request->get('name');
        $shortName = $request->request->get('shortName');
        $type = $request->request->get('type');
        $email = $request->request->get('email');
        $phone = $request->request->get('phone');
        $website = $request->request->get('website');

        $activeParam = $request->request->get('active');
        $active = $activeParam !== null ? filter_var($activeParam, FILTER_VALIDATE_BOOLEAN) : true;

        $address = $request->request->all('address');

        /** @var UploadedFile|null $logoFile */
        $logoFile = $request->files->get('logoFile');

        // Instanciation du DTO avec les données reçues
        $dto = new HealthcareOrganizationRequestDTO(
            name: $name,
            shortName: $shortName,
            type: $type,
            email: $email,
            phone: $phone,
            website: $website,
            logoFile: $logoFile,
            address: !empty($address) ? $address : null,
            active: $active
        );

        $feedback = $this->service->update($id, $dto);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}', name: 'api_healthcare_organizations_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Permet de supprimer définitivement une organisation de santé.',
        summary: 'Supprimer une organisation de santé'
    )]
    public function delete(string $id): JsonResponse
    {
        $feedback = $this->service->delete($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('/{id}/suspend', name: 'api_healthcare_organizations_suspend', methods: ['PATCH'])]
    #[OA\Patch(
        description: 'Permet de désactiver/suspendre une organisation de santé.',
        summary: 'Suspendre une organisation de santé'
    )]
    public function suspend(string $id): JsonResponse
    {
        $feedback = $this->service->suspend($id);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
