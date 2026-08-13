<?php

namespace App\Service\Healthcare;

use App\DTO\Feedback;
use App\DTO\Request\Healthcare\OrganizationAdministratorRequestDTO;
use App\Entity\Common\UserStatus;
use App\Entity\Healthcare\MembershipStatus;
use App\Entity\Healthcare\OrganizationMembership;
use App\Mapper\Healthcare\OrganizationAdministratorMapper;
use App\Repository\Healthcare\HealthcareOrganizationRepository;
use App\Repository\Identity\UserRepository;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OrganizationAdministratorService
{
    public function __construct(
        private readonly HealthcareOrganizationRepository $organizationRepository,
        private readonly UserRepository $userRepository,
        private readonly OrganizationAdministratorMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService,
    ) {}

    public function create(string $organizationId, OrganizationAdministratorRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            if (!$this->securityService->isSuperAdmin()) {
                throw new AccessDeniedException('Seul un administrateur système peut créer un administrateur d’organisation.');
            }

            $organization = $this->organizationRepository->find($organizationId);
            if (!$organization) {
                return $feedback->setErrorFlushDescription('Organisation introuvable.')->autoInitFlush();
            }

            if ($this->userRepository->findOneBy(['email' => $dto->email])) {
                return $feedback->setErrorFlushDescription('Un utilisateur utilise déjà cette adresse e-mail.')->autoInitFlush();
            }

            $administrator = $this->mapper->mapRequestToEntity($dto);
            $administrator->setStatus(UserStatus::ACTIVE);
            $administrator->setEmailVerifiedAt(new \DateTimeImmutable());

            $membership = (new OrganizationMembership())
                ->setOrganization($organization)
                ->setStartDate(new \DateTimeImmutable())
                ->setStatus(MembershipStatus::ACTIVE);
            $administrator->addOrganizationMembership($membership);

            $this->entityManager->persist($administrator);
            $this->entityManager->persist($membership);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($administrator))
                ->setFlushDescription('Administrateur d’organisation créé avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription('Erreur lors de la création : ' . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}
