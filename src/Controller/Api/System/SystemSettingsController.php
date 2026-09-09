<?php

declare(strict_types=1);

namespace App\Controller\Api\System;

use App\DTO\Request\System\SystemSettingsUpdateRequestDTO;
use App\DTO\Response\System\SystemSettingsResponseDTO;
use App\Service\System\SystemSettingsService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/settings')]
#[OA\Tag(
    name: 'System - Settings',
    description: 'Configuration globale de la plateforme (nom, logo, textes de la page d’accueil)'
)]
class SystemSettingsController extends AbstractController
{
    public function __construct(
        private readonly SystemSettingsService $settingsService,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ) {
    }

    #[Route('', name: 'api_system_settings_get', methods: ['GET'])]
    #[OA\Get(
        summary: 'Récupérer la configuration système',
        description: 'Endpoint public : renvoie l’identité visuelle (nom, logo) et les textes de la page d’accueil.'
    )]
    #[OA\Response(
        response: 200,
        description: 'Configuration récupérée avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(property: 'data', ref: new Model(type: SystemSettingsResponseDTO::class))
            ]
        )
    )]
    public function get(): JsonResponse
    {
        $feedback = $this->settingsService->get();

        $status = $feedback->hasErrors()
            ? Response::HTTP_BAD_REQUEST
            : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    #[Route('', name: 'api_system_settings_update', methods: ['PUT', 'POST', 'PATCH'])]
    #[OA\Put(
        summary: 'Mettre à jour la configuration système',
        description: 'Réservé au ROOT. Accepte du multipart/form-data (avec logo) ou du JSON. Les champs non fournis sont conservés.'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(ref: new Model(type: SystemSettingsUpdateRequestDTO::class))
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Configuration mise à jour avec succès',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'integer', example: 200),
                new OA\Property(property: 'error', type: 'boolean', example: false),
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(property: 'data', ref: new Model(type: SystemSettingsResponseDTO::class))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Données invalides')]
    #[OA\Response(response: 401, description: 'Non authentifié')]
    #[OA\Response(response: 403, description: 'Permission insuffisante (ROOT requis)')]
    public function update(Request $request): JsonResponse
    {
        $payload = $this->resolvePayload($request);

        $dto = $this->serializer->denormalize(
            $payload,
            SystemSettingsUpdateRequestDTO::class,
            null,
            ['allow_extra_attributes' => true]
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'error' => true,
                'message' => 'Données invalides',
                'errors' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $feedback = $this->settingsService->update($dto);

        $status = $feedback->hasErrors()
            ? Response::HTTP_BAD_REQUEST
            : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }

    /**
     * Fusionne les données de la requête (form-data ou JSON) et les fichiers uploadés.
     */
    private function resolvePayload(Request $request): array
    {
        $payload = $request->request->all();

        if ($request->getContentTypeFormat() === 'json') {
            $decoded = json_decode($request->getContent() ?: '{}', true);
            if (is_array($decoded)) {
                $payload = array_merge($payload, $decoded);
            }
        }

        return array_merge($payload, $request->files->all());
    }
}