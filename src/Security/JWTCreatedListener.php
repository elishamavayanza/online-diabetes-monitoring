<?php

namespace App\Security;

use App\Entity\Identity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $roles = $user->getRoles();

        // Rôles Symfony standard
        $payload['roles'] = $roles;

        // Rôle applicatif simplifié
        $payload['role'] = $this->mapRole($roles);

        // Permissions associées
        $payload['permissions'] = $this->getPermissionsForRole($roles);

        // Autres données utilisateur
        $payload['fullName'] = $user->getFullName();
        $payload['email'] = $user->getEmail();

        // Organisations actives (si nécessaires)
        $payload['organizations'] = $this->getActiveOrganizations($user);

        $event->setData($payload);
    }

    private function mapRole(array $roles): string
    {
        if (in_array('ROLE_ROOT', $roles, true)) return 'ROOT';
        if (in_array('ROLE_ADMIN', $roles, true)) return 'ADMIN';
        if (in_array('ROLE_CLINICIAN', $roles, true)) return 'CLINICIAN';
        if (in_array('ROLE_NUTRITIONIST', $roles, true)) return 'NUTRITIONIST';
        if (in_array('ROLE_PATIENT', $roles, true)) return 'PATIENT';
        return 'PATIENT';
    }

    private function getPermissionsForRole(array $roles): array
    {
        // Définissez ici les permissions par rôle, en cohérence avec votre SecurityService
        if (in_array('ROLE_ROOT', $roles, true)) {
            return ['DASHBOARD_VIEW', 'ORGANISATION_VIEW', 'USER_VIEW', 'ROLE_VIEW', 'SETTINGS_VIEW', 'NOTIFICATION_VIEW', 'AUDIT_VIEW'];
        }
        if (in_array('ROLE_ADMIN', $roles, true)) {
            return ['DASHBOARD_VIEW', 'ESTABLISHMENT_VIEW', 'DEPARTMENT_VIEW', 'PROFESSIONAL_VIEW', 'MEMBER_VIEW', 'PATIENT_VIEW', 'APPOINTMENT_VIEW', 'ACTIVITY_VIEW', 'SETTINGS_VIEW', 'NOTIFICATION_VIEW'];
        }
        if (in_array('ROLE_CLINICIAN', $roles, true)) {
            return ['DASHBOARD_VIEW', 'PATIENT_VIEW', 'APPOINTMENT_VIEW', 'MESSAGE_VIEW', 'NOTIFICATION_VIEW'];
        }
        if (in_array('ROLE_NUTRITIONIST', $roles, true)) {
            return ['DASHBOARD_VIEW', 'PATIENT_VIEW', 'NUTRITION_PLAN_VIEW', 'FOOD_VIEW', 'APPOINTMENT_VIEW', 'MESSAGE_VIEW', 'NOTIFICATION_VIEW'];
        }
        if (in_array('ROLE_PATIENT', $roles, true)) {
            return ['SUMMARY_VIEW', 'MEASUREMENT_VIEW', 'HEALTH_RECORD_VIEW', 'TREATMENT_VIEW', 'DOSE_VIEW', 'APPOINTMENT_VIEW', 'APPOINTMENT_CREATE', 'MESSAGE_VIEW', 'TEAM_VIEW', 'NOTIFICATION_VIEW'];
        }
        return [];
    }

    private function getActiveOrganizations(User $user): array
    {
        $organizations = [];
        foreach ($user->getOrganizationMemberships() as $membership) {
            if ($membership->getStatus()?->value === 'ACTIVE') {
                $organizations[] = [
                    'organization_id' => $membership->getOrganization()?->getId(),
                    'organization_name' => $membership->getOrganization()?->getName(),
                ];
            }
        }
        return $organizations;
    }
}
