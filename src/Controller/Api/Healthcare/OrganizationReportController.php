<?php

namespace App\Controller\Api\Healthcare;

use App\DTO\Response\Healthcare\Report\OrganizationReportDTO;
use App\DTO\Response\Healthcare\Report\OrganizationReportVerificationDTO;
use App\Service\Healthcare\OrganizationReportService;
use App\Service\Healthcare\Report\OrganizationReportVerificationService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/organization/reports')]
#[OA\Tag(
  name: 'Healthcare - Organization Reports',
  description: 'Rapport analytique global pour l\'administrateur de l\'organisation'
)]
class OrganizationReportController extends AbstractController
{
  public function __construct(
    private readonly OrganizationReportService $service,
    private readonly OrganizationReportVerificationService $verificationService,
  ) {}

  #[Route('/verify', name: 'api_organization_reports_verify', methods: ['GET'])]
  #[OA\Get(
    summary: 'Vérifier l\'authenticité d\'un rapport organisation',
    description: 'Endpoint public utilisé par le QR code imprimé sur le PDF.'
  )]
  #[OA\Parameter(name: 'ref', in: 'query', required: true, schema: new OA\Schema(type: 'string', example: 'RPT-ORG-12-20260830'))]
  #[OA\Parameter(name: 'organizationId', in: 'query', required: true, schema: new OA\Schema(type: 'string', example: '12'))]
  #[OA\Parameter(name: 'from', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date', example: '2026-08-01'))]
  #[OA\Parameter(name: 'to', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date', example: '2026-08-30'))]
  #[OA\Response(
    response: 200,
    description: 'Résultat de vérification',
    content: new OA\JsonContent(ref: new Model(type: OrganizationReportVerificationDTO::class))
  )]
  public function verify(Request $request): JsonResponse
  {
    $feedback = $this->verificationService->verify(
      $request->query->get('ref'),
      $request->query->get('organizationId'),
      $request->query->get('from'),
      $request->query->get('to'),
    );

    return $this->json($feedback, $feedback->getStatus());
  }

  #[Route('', name: 'api_organization_reports', methods: ['GET'])]
  #[OA\Get(
    summary: 'Générer le rapport analytique de l\'organisation',
    description: 'L\'organisation est résolue automatiquement depuis l\'utilisateur connecté (JWT).'
  )]
  #[OA\Parameter(name: 'from', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date', example: '2026-08-01'))]
  #[OA\Parameter(name: 'to', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date', example: '2026-08-30'))]
  #[OA\Parameter(
    name: 'period',
    in: 'query',
    required: false,
    schema: new OA\Schema(type: 'string', enum: ['month', 'quarter', 'year'], example: 'month')
  )]
  #[OA\Response(
    response: 200,
    description: 'Rapport généré',
    content: new OA\JsonContent(ref: new Model(type: OrganizationReportDTO::class))
  )]
  #[OA\Response(response: 403, description: 'Accès refusé')]
  public function getReport(Request $request): JsonResponse
  {
    $feedback = $this->service->generateReport(
      $request->query->get('from'),
      $request->query->get('to'),
      $request->query->get('period'),
    );

    return $this->json($feedback, $feedback->getStatus());
  }
}
