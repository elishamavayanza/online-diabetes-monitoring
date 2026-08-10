<?php

namespace App\Controller\Api;

use App\Security\SecurityAction;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Security - Permissions', description: 'Gestion et consultation des permissions du système')]
class PermissionController extends AbstractController
{
    #[Route('/api/permissions', name: 'api_permissions_list', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[OA\Get(
        path: '/api/permissions',
        summary: 'Récupérer la liste de toutes les permissions disponibles',
        security: [['Bearer' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des permissions récupérée avec succès',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'name', type: 'string', example: 'VIEW'),
                            new OA\Property(property: 'value', type: 'string', example: 'view')
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: 'Non authentifié')
        ]
    )]
    public function listPermissions(): JsonResponse
    {
        $permissions = [];

        foreach (SecurityAction::cases() as $action) {
            $permissions[] = [
                'name' => $action->name,   // Ex: 'VIEW_MEDICAL_RECORD'
                'value' => $action->value, // Ex: 'view_medical_record'
            ];
        }

        return new JsonResponse($permissions);
    }
}
