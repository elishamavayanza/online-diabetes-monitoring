<?php

namespace App\Controller\Api\Medical;

use App\Service\Common\ListQueryParams;
use App\Service\Medical\PatientDossierService;
use DateTimeImmutable;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/patients/{patientId}/dossier')]
#[OA\Tag(name: 'Medical - Patient Dossier', description: 'Dossier patient agrégé (évite le fan-out HTTP)')]
class PatientDossierController extends AbstractController
{
    public function __construct(
        private readonly PatientDossierService $service
    ) {
    }

    #[Route('', name: 'api_patient_dossier_get', methods: ['GET'])]
    #[OA\Get(
        summary: 'Récupérer le dossier patient agrégé',
        description: 'Retourne profil, mesures, repas, prescriptions et métadonnées médicales en un seul appel.'
    )]
    #[OA\Parameter(name: 'patientId', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'from', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date-time'))]
    #[OA\Parameter(name: 'to', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date-time'))]
    #[OA\Parameter(name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Dossier agrégé')]
    #[OA\Response(response: 403, description: 'Accès refusé')]
    #[OA\Response(response: 404, description: 'Patient introuvable')]
    public function get(string $patientId, Request $request): JsonResponse
    {
        $params = ListQueryParams::fromRequest($request);
        if ($params->from === null && $params->to === null && $params->limit === null) {
            $params = new ListQueryParams(
                from: new DateTimeImmutable('-365 days'),
                to: null,
                limit: 500,
            );
        }

        $feedback = $this->service->getDossier($patientId, $params);
        $status = $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK;

        return $this->json($feedback, $status);
    }
}
