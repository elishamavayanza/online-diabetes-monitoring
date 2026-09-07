<?php

namespace App\Security;

use App\Entity\Common\PatientCommonOperation;
use App\Entity\Identity\User;
use App\Entity\Medical\MedicalNote;
use App\Entity\Treatment\Prescription;
use App\Entity\Treatment\PrescriptionVersion;
use App\Entity\Appointment\Appointment;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Vérifie la règle métier : un professionnel ne peut modifier ou supprimer
 * qu'un enregistrement qu'il a lui-même créé. Tout le reste est en lecture seule.
 *
 * Fonctionne aussi bien pour le personnel interne que pour le professionnel
 * externe invité (EXTERNAL_FOLLOWER) : chacun n'édite que ses propres créations.
 */
class OwnershipGuardService
{
    public function __construct(
        private readonly SecurityServiceInterface $securityService
    ) {}

    /**
     * @param object $entity L'entité à vérifier.
     * @param User|null $currentUser Couramment l'utilisateur connecté (null => déterminé automatiquement).
     *
     * @throws AccessDeniedException si l'utilisateur n'est pas le créateur.
     */
    public function assertCreator(object $entity, ?User $currentUser = null): void
    {
        $user = $currentUser ?? $this->securityService->getCurrentUser();
        $creator = $this->resolveCreator($entity);

        if ($creator === null) {
            // Aucun auteur enregistré (données historiques) : on refuse l'édition
            // afin de préserver l'intégrité des données.
            throw new AccessDeniedException(
                'Vous ne pouvez pas modifier cet enregistrement : l’auteur est inconnu. Il est en lecture seule.'
            );
        }

        if ((string) $user->getId() !== (string) $creator->getId()) {
            throw new AccessDeniedException(
                'Vous ne pouvez modifier ou supprimer que les enregistrements que vous avez créés.'
            );
        }
    }

    /**
     * @return bool true si l'utilisateur courant est le créateur de l'entité.
     */
    public function isCreator(object $entity, ?User $currentUser = null): bool
    {
        $user = $currentUser ?? $this->securityService->getCurrentUser();
        $creator = $this->resolveCreator($entity);

        return $creator !== null && (string) $user->getId() === (string) $creator->getId();
    }

    private function resolveCreator(object $entity): ?User
    {
        if ($entity instanceof PatientCommonOperation) {
            return $entity->getIssuer();
        }

        if ($entity instanceof MedicalNote) {
            return $entity->getAuthor();
        }

        if ($entity instanceof Prescription) {
            return $entity->getPrescriber();
        }

        if ($entity instanceof PrescriptionVersion) {
            return $entity->getModifiedBy();
        }

        if ($entity instanceof Appointment) {
            return $entity->getProfessional();
        }

        if (method_exists($entity, 'getCreatedBy')) {
            $createdBy = $entity->getCreatedBy();
            return $createdBy instanceof User ? $createdBy : null;
        }

        return null;
    }
}
