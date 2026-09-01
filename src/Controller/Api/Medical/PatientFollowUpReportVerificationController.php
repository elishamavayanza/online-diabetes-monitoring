<?php

namespace App\Controller\Api\Medical;

use App\DTO\Response\Medical\Report\PatientFollowUpReportVerificationDTO;
use App\Service\Medical\Report\PatientFollowUpReportVerificationService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/patient-reports/follow-up')]
#[OA\Tag(name: 'Medical - Report Verification')]
class PatientFollowUpReportVerificationController extends AbstractController
{
    public function __construct(
        private readonly PatientFollowUpReportVerificationService $verificationService,
    ) {}

    #[Route('/verify', name: 'api_patient_follow_up_report_verify', methods: ['GET'])]
    #[OA\Get(
        description: 'Endpoint public utilisé par le QR code imprimé sur le PDF.',
        summary: 'Vérifier l\'authenticité d\'un rapport de suivi patient'
    )]
    #[OA\Parameter(name: 'ref', in: 'query', required: true, schema: new OA\Schema(type: 'string', example: 'RPT-PAT-42-20260831'))]
    #[OA\Parameter(name: 'patientId', in: 'query', required: true, schema: new OA\Schema(type: 'string', example: '42'))]
    #[OA\Parameter(name: 'from', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date', example: '2026-08-01'))]
    #[OA\Parameter(name: 'to', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date', example: '2026-08-31'))]
    #[OA\Response(
        response: 200,
        description: 'Résultat de vérification',
        content: new OA\JsonContent(ref: new Model(type: PatientFollowUpReportVerificationDTO::class))
    )]
    public function verify(Request $request): JsonResponse
    {
        $feedback = $this->verificationService->verify(
            $request->query->get('ref'),
            $request->query->get('patientId'),
            $request->query->get('from'),
            $request->query->get('to'),
        );

        return $this->json($feedback, $feedback->getStatus());
    }
}
