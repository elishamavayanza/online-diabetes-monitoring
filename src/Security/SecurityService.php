<?php

namespace App\Security;

use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Identity\Patient;
use App\Entity\Identity\User;
use App\Repository\Healthcare\CareTeamAssignmentRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class SecurityService implements SecurityServiceInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly CareTeamAssignmentRepository $careTeamAssignmentRepository,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | CURRENT USER
    |--------------------------------------------------------------------------
    */

    public function getCurrentUser(): User
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedException(
                'Utilisateur non authentifié.'
            );
        }

        return $user;
    }

    public function isAuthenticated(): bool
    {
        return $this->security->getUser() instanceof User;
    }

    /*
    |--------------------------------------------------------------------------
    | ROLES
    |--------------------------------------------------------------------------
    */

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('ROLE_ROOT');
    }

    public function isOrganizationAdmin(): bool
    {
        return $this->hasRole('ROLE_ADMIN');
    }

    public function isClinician(): bool
    {
        return $this->hasRole('ROLE_CLINICIAN');
    }

    public function isNutritionist(): bool
    {
        return $this->hasRole('ROLE_NUTRITIONIST');
    }

    public function isPatient(): bool
    {
        return $this->hasRole('ROLE_PATIENT');
    }

    public function hasRole(string $role): bool
    {
        return $this->security->isGranted($role);
    }

    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | ORGANIZATION
    |--------------------------------------------------------------------------
    */

    public function checkOrganizationAccess(
        HealthcareOrganization $organization,
        SecurityAction $action
    ): void {
        /*
         * ----------------------------------------------------------
         * 1. SUPER ADMIN
         * ----------------------------------------------------------
         *
         * Le Super Admin possède les privilèges plateforme.
         */
        if ($this->isSuperAdmin()) {
            return;
        }

        /*
         * ----------------------------------------------------------
         * 2. AUTHENTIFICATION
         * ----------------------------------------------------------
         */

        $user = $this->getCurrentUser();

        /*
         * ----------------------------------------------------------
         * 3. ORGANIZATION ACTIVE
         * ----------------------------------------------------------
         */

        $this->checkOrganizationActive($organization);

        /*
         * ----------------------------------------------------------
         * 4. MULTI-TENANT
         * ----------------------------------------------------------
         *
         * L'utilisateur doit avoir un membership actif
         * dans cette organisation.
         */
        if (!$this->belongsToOrganization($user, $organization)) {
            throw new AccessDeniedException(
                'Accès refusé : vous n’appartenez pas à cette organisation.'
            );
        }

        /*
         * ----------------------------------------------------------
         * 5. ORGANIZATION ADMIN
         * ----------------------------------------------------------
         */

        if ($this->isOrganizationAdmin()) {
            $this->checkOrganizationAdminAction($action);

            return;
        }

        /*
         * ----------------------------------------------------------
         * 6. CLINICIAN
         * ----------------------------------------------------------
         */

        if ($this->isClinician()) {
            $this->checkClinicianAction($action);

            return;
        }

        /*
         * ----------------------------------------------------------
         * 7. NUTRITIONIST
         * ----------------------------------------------------------
         */

        if ($this->isNutritionist()) {
            $this->checkNutritionistAction($action);

            return;
        }

        /*
         * ----------------------------------------------------------
         * 8. PATIENT
         * ----------------------------------------------------------
         */

        if ($this->isPatient()) {
            $this->checkPatientAction($action);

            return;
        }

        /*
         * ----------------------------------------------------------
         * 9. DENY BY DEFAULT
         * ----------------------------------------------------------
         */

        throw new AccessDeniedException(
            sprintf(
                'Accès refusé pour l’action "%s".',
                $action->value
            )
        );
    }

    public function checkOrganizationActive(
        HealthcareOrganization $organization
    ): void {
        if (!$organization->isActive() && !$this->isSuperAdmin()) {
            throw new AccessDeniedException(
                sprintf(
                    'L’organisation "%s" est désactivée.',
                    $organization->getName()
                )
            );
        }
    }

    public function checkCurrentUserOrganizationActive(): void
    {
        $user = $this->getCurrentUser();

        foreach ($user->getOrganizationMemberships() as $membership) {
            if (!$membership->getStatus()->isActive()) {
                continue;
            }

            $organization = $membership->getOrganization();

            if ($organization === null) {
                continue;
            }

            $this->checkOrganizationActive($organization);
        }
    }

    public function belongsToOrganization(
        User $user,
        HealthcareOrganization $organization
    ): bool {
        foreach ($user->getOrganizationMemberships() as $membership) {
            if ($membership->getOrganization()?->getId()
                !== $organization->getId()) {
                continue;
            }

            if (!$membership->getStatus()->isActive()) {
                continue;
            }

            return true;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | PATIENT
    |--------------------------------------------------------------------------
    */

    public function checkPatientAccess(
        Patient $patient,
        SecurityAction $action
    ): void {
        /*
         * SUPER ADMIN
         */
        if ($this->isSuperAdmin()) {
            return;
        }

        $user = $this->getCurrentUser();

        /*
         * PATIENT
         *
         * Un patient ne peut accéder qu'à ses propres données.
         */
        if ($this->isPatient()) {
            if (!$this->isPatientOwner($user, $patient)) {
                throw new AccessDeniedException(
                    'Vous ne pouvez accéder qu’à vos propres données.'
                );
            }

            $this->checkPatientAction($action);

            return;
        }

        /*
         * ORGANIZATION ADMIN
         *
         * L'admin doit appartenir à l'organisation
         * du patient.
         */
        if ($this->isOrganizationAdmin()) {
            $organization = $this->getPatientOrganization($patient);

            if ($organization === null) {
                throw new AccessDeniedException(
                    'Le patient n’est associé à aucune organisation.'
                );
            }

            $this->checkOrganizationAccess(
                $organization,
                $action
            );

            return;
        }

        /*
         * PROFESSIONAL
         *
         * Clinician / Nutritionist doivent être
         * associés au patient via CareTeamAssignment.
         */
        if ($this->isClinician() || $this->isNutritionist()) {

            if (!$this->isAssignedToPatient($user, $patient)) {
                throw new AccessDeniedException(
                    'Vous n’êtes pas affecté à ce patient.'
                );
            }

            if ($this->isClinician()) {
                $this->checkClinicianAction($action);

                return;
            }

            if ($this->isNutritionist()) {
                $this->checkNutritionistAction($action);

                return;
            }
        }

        throw new AccessDeniedException(
            'Accès refusé aux données du patient.'
        );
    }

    public function isPatientOwner(
        User $user,
        Patient $patient
    ): bool {
        return $user->getId() === $patient->getId();
    }

    public function isAssignedToPatient(
        User $user,
        Patient $patient
    ): bool {
        return $this->careTeamAssignmentRepository
            ->isUserActivelyAssignedToPatient($user->getId(), $patient);
    }

    /*
    |--------------------------------------------------------------------------
    | PROFESSIONAL
    |--------------------------------------------------------------------------
    */

    public function checkProfessionalAccess(
        SecurityAction $action
    ): void {
        if ($this->isSuperAdmin()) {
            return;
        }

        if ($this->isClinician()) {
            $this->checkClinicianAction($action);

            return;
        }

        if ($this->isNutritionist()) {
            $this->checkNutritionistAction($action);

            return;
        }

        throw new AccessDeniedException(
            'Cette action nécessite un rôle professionnel.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PERMISSIONS
    |--------------------------------------------------------------------------
    */

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Convertir la chaîne en SecurityAction
        $action = SecurityAction::tryFrom($permission);
        if (!$action) {
            return false;
        }

        try {
            if ($this->isOrganizationAdmin()) {
                $this->checkOrganizationAdminAction($action);
                return true;
            }

            if ($this->isClinician()) {
                $this->checkClinicianAction($action);
                return true;
            }

            if ($this->isNutritionist()) {
                $this->checkNutritionistAction($action);
                return true;
            }

            if ($this->isPatient()) {
                $this->checkPatientAction($action);
                return true;
            }
        } catch (AccessDeniedException $e) {
            return false;
        }

        return false;
    }

    public function checkPermission(string $permission): void
    {
        if (!$this->hasPermission($permission)) {
            throw new AccessDeniedException(
                sprintf(
                    'Permission refusée : "%s".',
                    $permission
                )
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | COMBINED CHECKS
    |--------------------------------------------------------------------------
    */

    public function checkOrganizationAccessAndActive(
        HealthcareOrganization $organization,
        SecurityAction $action
    ): void {
        $this->checkOrganizationAccess(
            $organization,
            $action
        );

        $this->checkOrganizationActive(
            $organization
        );
    }

    public function checkPatientAccessAndOrganization(
        Patient $patient,
        SecurityAction $action
    ): void {
        $organization = $this->getPatientOrganization($patient);

        if ($organization === null) {
            throw new AccessDeniedException(
                'Le patient n’est associé à aucune organisation.'
            );
        }

        $this->checkOrganizationAccessAndActive(
            $organization,
            $action
        );

        $this->checkPatientAccess(
            $patient,
            $action
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ORGANIZATION ADMIN RULES
    |--------------------------------------------------------------------------
    */

    private function checkOrganizationAdminAction(
        SecurityAction $action
    ): void {
        $allowed = [
            SecurityAction::VIEW,

            SecurityAction::CREATE,
            SecurityAction::UPDATE,

            SecurityAction::MANAGE_ORGANIZATION,
            SecurityAction::MANAGE_FACILITY,
            SecurityAction::MANAGE_DEPARTMENT,
            SecurityAction::VIEW_MEDICATION,

            SecurityAction::MANAGE_USERS,
            SecurityAction::SUSPEND_USER,
            SecurityAction::ACTIVATE_USER,

            SecurityAction::VIEW_PATIENT,
            SecurityAction::UPDATE_PATIENT,
            SecurityAction::TRANSFER_PATIENT,
            SecurityAction::ARCHIVE_PATIENT,
            SecurityAction::ACTIVATE_PATIENT,

            SecurityAction::VIEW_MEDICAL_RECORD,
            SecurityAction::CREATE_MEDICAL_RECORD,

            SecurityAction::VIEW_APPOINTMENT,
            SecurityAction::CREATE_APPOINTMENT,
            SecurityAction::UPDATE_APPOINTMENT,
            SecurityAction::CANCEL_APPOINTMENT,
            SecurityAction::CONFIRM_APPOINTMENT,
            SecurityAction::REQUEST_RESCHEDULE,
            SecurityAction::DELETE_APPOINTMENT,

            SecurityAction::VIEW_APPOINTMENT_REMINDER,
            SecurityAction::CREATE_APPOINTMENT_REMINDER,
            SecurityAction::UPDATE_APPOINTMENT_REMINDER,
            SecurityAction::DELETE_APPOINTMENT_REMINDER,

            SecurityAction::VIEW_NUTRITION,

            SecurityAction::SEND_MESSAGE,
            SecurityAction::READ_MESSAGE,

            SecurityAction::CREATE_NOTIFICATION,
        ];

        $this->denyIfNotAllowed(
            $action,
            $allowed,
            'Administrateur d’organisation'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CLINICIAN RULES
    |--------------------------------------------------------------------------
    */

    private function checkClinicianAction(
        SecurityAction $action
    ): void {
        $allowed = [
            SecurityAction::VIEW,
            SecurityAction::VIEW_PATIENT,
            SecurityAction::VIEW_NUTRITION,
            SecurityAction::VIEW_MEDICAL_RECORD,
            SecurityAction::CREATE_MEDICAL_RECORD,
            SecurityAction::VIEW_MEDICAL_NOTES,
            SecurityAction::EDIT_MEDICAL_NOTE,
            SecurityAction::DELETE_MEDICAL_NOTE,

            SecurityAction::CREATE_DIAGNOSIS,
            SecurityAction::UPDATE_DIAGNOSIS,
            SecurityAction::CREATE_MEDICAL_NOTE,

            SecurityAction::MANAGE_MEDICATION,
            SecurityAction::DELETE_MEDICATION_INTAKE,
            SecurityAction::VIEW_MEDICATION,

            SecurityAction::RECORD_GLUCOSE,
            SecurityAction::RECORD_BLOOD_PRESSURE,
            SecurityAction::RECORD_HBA1C,
            SecurityAction::RECORD_WEIGHT,
            SecurityAction::RECORD_ACTIVITY,

            SecurityAction::VIEW_MEASUREMENTS,

            SecurityAction::VIEW_LABORATORY_RESULT,
            SecurityAction::UPLOAD_LABORATORY_RESULT,

            SecurityAction::VIEW_PRESCRIPTION,
            SecurityAction::CREATE_PRESCRIPTION,
            SecurityAction::UPDATE_PRESCRIPTION,
            SecurityAction::CANCEL_PRESCRIPTION,
            SecurityAction::VALIDATE_PRESCRIPTION,

            SecurityAction::VIEW_APPOINTMENT,
            SecurityAction::CREATE_APPOINTMENT,
            SecurityAction::UPDATE_APPOINTMENT,
            SecurityAction::CANCEL_APPOINTMENT,
            SecurityAction::CONFIRM_APPOINTMENT,
            SecurityAction::REQUEST_RESCHEDULE,
            SecurityAction::DELETE_APPOINTMENT,

            SecurityAction::VIEW_APPOINTMENT_REMINDER,
            SecurityAction::CREATE_APPOINTMENT_REMINDER,
            SecurityAction::UPDATE_APPOINTMENT_REMINDER,
            SecurityAction::DELETE_APPOINTMENT_REMINDER,

            SecurityAction::SEND_MESSAGE,
            SecurityAction::READ_MESSAGE,
            SecurityAction::DOWNLOAD_ATTACHMENT,
            SecurityAction::CREATE_CONVERSATION,

            SecurityAction::CREATE_ALLERGY,
            SecurityAction::VIEW_ALLERGY,
            SecurityAction::UPDATE_ALLERGY,
            SecurityAction::DELETE_ALLERGY,
            SecurityAction::VIEW_EMERGENCY_CONTACT,
            SecurityAction::VIEW_MEDICAL_CONSENT,

            SecurityAction::VIEW_MEDICAL_CONSENT,
            SecurityAction::CREATE_MEDICAL_CONSENT,
            SecurityAction::REVOKE_MEDICAL_CONSENT,

            SecurityAction::CREATE_REMINDER_RULE,

            SecurityAction::CREATE_NOTIFICATION,
        ];

        $this->denyIfNotAllowed(
            $action,
            $allowed,
            'Clinicien'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | NUTRITIONIST RULES
    |--------------------------------------------------------------------------
    */

    private function checkNutritionistAction(
        SecurityAction $action
    ): void {
        $allowed = [
            SecurityAction::VIEW,
            SecurityAction::VIEW_PATIENT,
            SecurityAction::VIEW_NUTRITION,
            SecurityAction::VIEW_MEDICAL_RECORD,
            SecurityAction::CREATE_MEDICAL_RECORD,
            SecurityAction::VIEW_MEDICAL_NOTES,
            SecurityAction::EDIT_MEDICAL_NOTE,
            SecurityAction::DELETE_MEDICAL_NOTE,

            SecurityAction::CREATE_DIAGNOSIS,
            SecurityAction::UPDATE_DIAGNOSIS,
            SecurityAction::CREATE_MEDICAL_NOTE,

            SecurityAction::MANAGE_MEDICATION,
            SecurityAction::DELETE_MEDICATION_INTAKE,
            SecurityAction::VIEW_MEDICATION,

            SecurityAction::RECORD_GLUCOSE,
            SecurityAction::RECORD_BLOOD_PRESSURE,
            SecurityAction::RECORD_HBA1C,
            SecurityAction::RECORD_WEIGHT,
            SecurityAction::RECORD_ACTIVITY,

            SecurityAction::VIEW_MEASUREMENTS,

            SecurityAction::VIEW_LABORATORY_RESULT,
            SecurityAction::UPLOAD_LABORATORY_RESULT,

            SecurityAction::VIEW_PRESCRIPTION,
            SecurityAction::CREATE_PRESCRIPTION,
            SecurityAction::UPDATE_PRESCRIPTION,
            SecurityAction::CANCEL_PRESCRIPTION,
            SecurityAction::VALIDATE_PRESCRIPTION,

            SecurityAction::VIEW_APPOINTMENT,
            SecurityAction::CREATE_APPOINTMENT,
            SecurityAction::UPDATE_APPOINTMENT,
            SecurityAction::CANCEL_APPOINTMENT,
            SecurityAction::CONFIRM_APPOINTMENT,
            SecurityAction::REQUEST_RESCHEDULE,
            SecurityAction::DELETE_APPOINTMENT,

            SecurityAction::VIEW_APPOINTMENT_REMINDER,
            SecurityAction::CREATE_APPOINTMENT_REMINDER,
            SecurityAction::UPDATE_APPOINTMENT_REMINDER,
            SecurityAction::DELETE_APPOINTMENT_REMINDER,

            SecurityAction::SEND_MESSAGE,
            SecurityAction::READ_MESSAGE,
            SecurityAction::DOWNLOAD_ATTACHMENT,
            SecurityAction::CREATE_CONVERSATION,

            SecurityAction::CREATE_ALLERGY,
            SecurityAction::VIEW_ALLERGY,
            SecurityAction::UPDATE_ALLERGY,
            SecurityAction::DELETE_ALLERGY,
            SecurityAction::VIEW_EMERGENCY_CONTACT,
            SecurityAction::VIEW_MEDICAL_CONSENT,
            SecurityAction::CREATE_MEDICAL_CONSENT,
            SecurityAction::REVOKE_MEDICAL_CONSENT,

            SecurityAction::MANAGE_FOOD,
            SecurityAction::MANAGE_FOOD_CATEGORY,
            SecurityAction::MANAGE_MEAL,
            SecurityAction::CREATE_NUTRITION_ADVICE,

            SecurityAction::CREATE_REMINDER_RULE,

            SecurityAction::CREATE_NOTIFICATION,
        ];

        $this->denyIfNotAllowed(
            $action,
            $allowed,
            'Nutritionniste'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PATIENT RULES
    |--------------------------------------------------------------------------
    */

    private function checkPatientAction(
        SecurityAction $action
    ): void {
        $allowed = [
            SecurityAction::VIEW_PATIENT,
            SecurityAction::UPDATE_PATIENT,

            SecurityAction::VIEW_MEDICAL_RECORD,
            SecurityAction::VIEW_MEDICAL_NOTES,
            SecurityAction::EDIT_MEDICAL_NOTE,

            SecurityAction::RECORD_GLUCOSE,
            SecurityAction::RECORD_WEIGHT,
            SecurityAction::RECORD_BLOOD_PRESSURE,
            SecurityAction::RECORD_ACTIVITY,

            SecurityAction::RECORD_MEDICATION_INTAKE,

            SecurityAction::VIEW_MEASUREMENTS,

            SecurityAction::VIEW_NUTRITION,
            SecurityAction::MANAGE_MEAL,

            SecurityAction::VIEW_PRESCRIPTION,
            SecurityAction::VIEW_MEDICATION,

            SecurityAction::VIEW_ALLERGY,

            SecurityAction::VIEW_LABORATORY_RESULT,
            SecurityAction::CREATE_APPOINTMENT,

            SecurityAction::VIEW_APPOINTMENT,
            SecurityAction::CANCEL_APPOINTMENT,
            SecurityAction::CONFIRM_APPOINTMENT,
            SecurityAction::REQUEST_RESCHEDULE,

            SecurityAction::SEND_MESSAGE,
            SecurityAction::READ_MESSAGE,
            SecurityAction::DOWNLOAD_ATTACHMENT,
            SecurityAction::CREATE_CONVERSATION,

            SecurityAction::VIEW_NOTIFICATION,
            SecurityAction::MARK_NOTIFICATION_READ,

            // Gestion complète de ses Allergies
            SecurityAction::CREATE_ALLERGY,
            SecurityAction::VIEW_ALLERGY,
            SecurityAction::UPDATE_ALLERGY,
            SecurityAction::DELETE_ALLERGY,

            // Gestion complète de ses Contacts d'urgence
            SecurityAction::CREATE_EMERGENCY_CONTACT,
            SecurityAction::VIEW_EMERGENCY_CONTACT,
            SecurityAction::UPDATE_EMERGENCY_CONTACT,
            SecurityAction::DELETE_EMERGENCY_CONTACT,

            // Gestion de ses Consentements
            SecurityAction::CREATE_MEDICAL_CONSENT,
            SecurityAction::VIEW_MEDICAL_CONSENT,
            SecurityAction::REVOKE_MEDICAL_CONSENT,

            // Gestion de sa Diagnostique
            SecurityAction::CREATE_DIAGNOSIS,
            SecurityAction::UPDATE_DIAGNOSIS,

            SecurityAction::CREATE_REMINDER_RULE,
        ];

        $this->denyIfNotAllowed(
            $action,
            $allowed,
            'Patient'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERIC DENY
    |--------------------------------------------------------------------------
    */

    private function denyIfNotAllowed(
        SecurityAction $action,
        array $allowed,
        string $roleName
    ): void {
        if (!in_array($action, $allowed, true)) {
            throw new AccessDeniedException(
                sprintf(
                    '%s n’est pas autorisé à effectuer l’action "%s".',
                    $roleName,
                    $action->value
                )
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PATIENT ORGANIZATION
    |--------------------------------------------------------------------------
    */

    private function getPatientOrganization(
        Patient $patient
    ): ?HealthcareOrganization {
        /*
         * Le patient peut avoir plusieurs memberships.
         *
         * On recherche une organisation active.
         */
        foreach ($patient->getOrganizationMemberships() as $membership) {

            if (!$membership->getStatus()->isActive()) {
                continue;
            }

            $organization = $membership->getOrganization();

            if ($organization !== null) {
                return $organization;
            }
        }

        return null;
    }
}
