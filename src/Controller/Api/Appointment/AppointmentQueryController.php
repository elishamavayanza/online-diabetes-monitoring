<?php

namespace App\Controller\Api\Appointment;

use App\Repository\Identity\PatientRepository;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Service\Appointment\AppointmentService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/appointments/queries')]
#[OA\Tag(
    name: 'Appointment Queries',
    description: 'Consultation et recherche de rendez-vous')]
class AppointmentQueryController extends AbstractController
{
    public function __construct(
        private readonly AppointmentService $service,
        private readonly PatientRepository $patientRepository,
        private readonly HealthcareOrganizationRepository $organizationRepository
    ) {}

    #[Route('/patient/{patientId}', name: 'api_appointments_by_patient', methods: ['GET'])]
    #[OA\Get(
        description: 'Permet à un patient de consulter la liste de ses rendez-vous.',
        summary: "Voir les rendez-vous d'un patient"
    )]
    #[OA\Response(response: 200, description: 'Liste récupérée avec succès')]
    #[OA\Response(response: 404, description: 'Patient introuvable')]
    public function getForPatient(int $patientId): JsonResponse
    {
        $patient = $this->patientRepository->find($patientId);
        if (!$patient) {
            return $this->json([
                'status' => 404,
                'error' => true,
                'message' => 'Patient introuvable.',
                'data' => null
            ], Response::HTTP_NOT_FOUND);
        }

        $feedback = $this->service->getPatientAppointments($patient);
        return $this->json($feedback, $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK);
    }

    #[Route('/organization/{organizationId}', name: 'api_appointments_by_organization', methods: ['GET'])]
    #[OA\Get(
        description: "Permet à un médecin ou un administrateur de voir les rendez-vous d'une organisation.",
        summary: "Voir les rendez-vous d'une organisation (Médecin/Admin)"
    )]
    #[OA\Response(response: 200, description: 'Liste récupérée avec succès')]
    #[OA\Response(response: 403, description: 'Accès refusé')]
    #[OA\Response(response: 404, description: 'Organisation introuvable')]
    public function getForOrganization(int $organizationId): JsonResponse
    {
        $organization = $this->organizationRepository->find($organizationId);
        if (!$organization) {
            return $this->json([
                'status' => 404,
                'error' => true,
                'message' => 'Organisation introuvable.',
                'data' => null
            ], Response::HTTP_NOT_FOUND);
        }

        $feedback = $this->service->getProfessionalOrAdminAppointments($organization);
        return $this->json($feedback, $feedback->hasErrors() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK);
    }
}
