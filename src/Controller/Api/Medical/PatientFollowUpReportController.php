<?php

namespace App\Controller\Api\Medical;

use App\DTO\Response\Medical\Report\PatientFollowUpReportDTO;
use App\Service\Medical\PatientFollowUpReportService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/patients/{patientId}/reports/follow-up')]
#[OA\Tag(
    name: 'Medical - Patient Follow-up Reports',
    description: 'Rapport périodique d\'évolution d\'un patient diabétique'
)]
class PatientFollowUpReportController extends AbstractController
{
    public function __construct(
        private readonly PatientFollowUpReportService $service,
    ) {}

    #[Route('', name: 'api_patient_follow_up_report', methods: ['GET'])]
    #[OA\Get(
        summary: 'Générer les données du rapport de suivi patient',
        description: 'Retourne les données agrégées pour la période et les éléments sélectionnés.'
    )]
    #[OA\Parameter(name: 'patientId', in: 'path', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'from', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date', example: '2026-08-01'))]
    #[OA\Parameter(name: 'to', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date', example: '2026-08-31'))]
    #[OA\Parameter(
        name: 'elements[]',
        in: 'query',
        required: true,
        schema: new OA\Schema(
            type: 'array',
            items: new OA\Items(
                type: 'string',
                enum: ['glucose', 'hba1c', 'blood_pressure', 'weight', 'treatment', 'physical_activity', 'nutrition', 'laboratory']
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Rapport généré',
        content: new OA\JsonContent(ref: new Model(type: PatientFollowUpReportDTO::class))
    )]
    #[OA\Response(response: 400, description: 'Paramètres invalides')]
    #[OA\Response(response: 403, description: 'Accès refusé')]
    #[OA\Response(response: 404, description: 'Patient introuvable')]
    public function getReport(string $patientId, Request $request): JsonResponse
    {
        $feedback = $this->service->generateReport(
            $patientId,
            $request->query->get('from'),
            $request->query->get('to'),
            $this->extractElements($request),
        );

        return $this->json($feedback, $feedback->getStatus());
    }

    /**
     * @return string[]
     */
    private function extractElements(Request $request): array
    {
        $elements = $request->query->all('elements');

        if ($elements !== []) {
            return array_values(array_filter(array_map('strval', $elements)));
        }

        $raw = $request->query->get('elements');
        if (is_string($raw) && $raw !== '') {
            return [$raw];
        }

        return [];
    }
}
