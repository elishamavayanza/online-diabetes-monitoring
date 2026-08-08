<?php

namespace App\Security;

use App\Entity\Identity\User;
use App\Entity\Healthcare\HealthcareOrganization;
use App\Entity\Patient\Patient;

interface SecurityServiceInterface
{
    /*
     * ============================================================
     * CURRENT USER
     * ============================================================
     */

    public function getCurrentUser(): User;

    public function isAuthenticated(): bool;

    /*
     * ============================================================
     * ROLES
     * ============================================================
     */

    public function isSuperAdmin(): bool;

    public function isOrganizationAdmin(): bool;

    public function isClinician(): bool;

    public function isNutritionist(): bool;

    public function isPatient(): bool;

    public function hasRole(string $role): bool;

    public function hasAnyRole(array $roles): bool;

    /*
     * ============================================================
     * ORGANIZATION
     * ============================================================
     */

    public function checkOrganizationAccess(
        HealthcareOrganization $organization,
        SecurityAction $action
    ): void;

    public function checkOrganizationActive(
        HealthcareOrganization $organization
    ): void;

    public function checkCurrentUserOrganizationActive(): void;

    public function belongsToOrganization(
        User $user,
        HealthcareOrganization $organization
    ): bool;

    /*
     * ============================================================
     * PATIENT
     * ============================================================
     */

    public function checkPatientAccess(
        Patient $patient,
        SecurityAction $action
    ): void;

    public function isPatientOwner(
        User $user,
        Patient $patient
    ): bool;

    public function isAssignedToPatient(
        User $user,
        Patient $patient
    ): bool;

    /*
     * ============================================================
     * PROFESSIONAL
     * ============================================================
     */

    public function checkProfessionalAccess(
        SecurityAction $action
    ): void;

    /*
     * ============================================================
     * PERMISSION
     * ============================================================
     *
     * Ces méthodes permettront plus tard de brancher
     * RolePermission / UserPermission.
     */

    public function hasPermission(string $permission): bool;

    public function checkPermission(string $permission): void;

    /*
     * ============================================================
     * COMBINED CHECKS
     * ============================================================
     */

    public function checkOrganizationAccessAndActive(
        HealthcareOrganization $organization,
        SecurityAction $action
    ): void;

    public function checkPatientAccessAndOrganization(
        Patient $patient,
        SecurityAction $action
    ): void;
}
