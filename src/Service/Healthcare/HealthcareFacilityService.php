<?php

namespace App\Service\Healthcare;

use App\DTO\Feedback;
use App\DTO\Request\Healthcare\HealthcareFacilityRequestDTO;
use App\Mapper\Healthcare\HealthcareFacilityMapper;
use App\Repository\Healthcare\HealthcareFacilityRepository;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HealthcareFacilityService
{
    public function __construct(
        private readonly HealthcareFacilityRepository $facilityRepository,
        private readonly HealthcareOrganizationRepository $organizationRepository,
        private readonly HealthcareFacilityMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {}

    public function create(HealthcareFacilityRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_FACILITY->value);

            $organization = $this->organizationRepository->find($dto->organizationId);
            if (!$organization) {
                return $feedback->setErrorFlushDescription("Organisation introuvable.")->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess($organization, SecurityAction::MANAGE_FACILITY);

            $facility = $this->mapper->mapRequestToEntity($dto, $organization);

            $this->entityManager->persist($facility);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($facility))
                ->setFlushDescription("Établissement de santé créé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(string $id, HealthcareFacilityRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_FACILITY->value);

            $facility = $this->facilityRepository->find($id);
            if (!$facility) {
                return $feedback->setErrorFlushDescription("Établissement de santé introuvable.")->autoInitFlush();
            }

            $organization = $this->organizationRepository->find($dto->organizationId);
            if (!$organization) {
                return $feedback->setErrorFlushDescription("Organisation introuvable.")->autoInitFlush();
            }

            $this->securityService->checkOrganizationAccess($organization, SecurityAction::MANAGE_FACILITY);

            // Mise à jour des propriétés via le mapper ou directement
            $facility->setName($dto->name);
            $facility->setPhone($dto->phone);
            $facility->setOrganization($organization);
            // Mettez à jour l'adresse si nécessaire selon votre implémentation

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($facility))
                ->setFlushDescription("Établissement de santé mis à jour avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function getByOrganization(string $organizationId): Feedback
    {
        $feedback = new Feedback();

        try {
            $organization = $this->organizationRepository->find($organizationId);
            if (!$organization) {
                return $feedback->setErrorFlushDescription("Organisation introuvable.")->autoInitFlush();
            }

            $facilities = $this->facilityRepository->findBy(['organization' => $organization]);

            $responseDTOs = array_map(
                fn($facility) => $this->mapper->mapEntityToResponse($facility),
                $facilities
            );

            $feedback->setData($responseDTOs)
                ->setFlushDescription("Liste des établissements récupérée avec succès.")
                ->autoInitFlush();

        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function toggleSuspend(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::MANAGE_FACILITY->value);

            $facility = $this->facilityRepository->find($id);
            if (!$facility) {
                return $feedback->setErrorFlushDescription("Établissement de santé introuvable.")->autoInitFlush();
            }

            // Supposons qu'il y a un champ/méthode isActive ou qu'on gère un statut de suspension
            // Si vous avez un champ 'active' dans HealthcareFacility, adaptez cette ligne :
            $newStatus = !$facility->isActive(); // Ajustez selon la propriété exacte de votre entité
            $facility->setActive($newStatus);

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($facility))
                ->setFlushDescription($newStatus ? "Établissement réactivé avec succès." : "Établissement suspendu avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
