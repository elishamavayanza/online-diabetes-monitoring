import { RoleData, RoleId } from '../types';

// Simulation de données (à remplacer par un vrai appel API)
export async function fetchRolesData(): Promise<RoleData> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return {
        roles: [
            {
                id: 'ROOT',
                label: 'Root',
                permissions: ['DASHBOARD_VIEW', 'ORGANISATION_VIEW', 'USER_VIEW', 'ROLE_VIEW', 'SETTINGS_VIEW', 'NOTIFICATION_VIEW', 'AUDIT_VIEW'],
            },
            {
                id: 'ADMIN',
                label: 'Admin',
                permissions: ['DASHBOARD_VIEW', 'ESTABLISHMENT_VIEW', 'DEPARTMENT_VIEW', 'PROFESSIONAL_VIEW', 'MEMBER_VIEW', 'PATIENT_VIEW', 'APPOINTMENT_VIEW', 'ACTIVITY_VIEW', 'SETTINGS_VIEW', 'NOTIFICATION_VIEW'],
            },
            {
                id: 'CLINICIAN',
                label: 'Clinicien',
                permissions: ['DASHBOARD_VIEW', 'PATIENT_VIEW', 'APPOINTMENT_VIEW', 'MESSAGE_VIEW', 'NOTIFICATION_VIEW'],
            },
            {
                id: 'NUTRITIONIST',
                label: 'Nutritionniste',
                permissions: ['DASHBOARD_VIEW', 'PATIENT_VIEW', 'NUTRITION_PLAN_VIEW', 'FOOD_VIEW', 'APPOINTMENT_VIEW', 'MESSAGE_VIEW', 'NOTIFICATION_VIEW'],
            },
            {
                id: 'PATIENT',
                label: 'Patient',
                permissions: ['SUMMARY_VIEW', 'MEASUREMENT_VIEW', 'HEALTH_RECORD_VIEW', 'TREATMENT_VIEW', 'DOSE_VIEW', 'APPOINTMENT_VIEW', 'APPOINTMENT_CREATE', 'MESSAGE_VIEW', 'TEAM_VIEW', 'NOTIFICATION_VIEW'],
            },
        ],
        usersByRole: {
            ROOT: [{ id: 'u1', nom: 'Admin Principal', email: 'root@diabcare.com' }],
            ADMIN: [
                { id: 'u2', nom: 'Admin Hôpital B', email: 'admin.hopital@diabcare.com' },
                { id: 'u3', nom: 'Admin Clinique A', email: 'admin.clinique@diabcare.com' },
            ],
            CLINICIAN: [
                { id: 'u4', nom: 'Dr. Jean Mukendi', email: 'jean.mukendi@diabcare.com' },
            ],
            NUTRITIONIST: [
                { id: 'u5', nom: 'Nutritionniste Sarah', email: 'sarah@diabcare.com' },
            ],
            PATIENT: [
                { id: 'u6', nom: 'Marie Zawadi', email: 'marie.zawadi@diabcare.com' },
                { id: 'u7', nom: 'Patient Test', email: 'patient.test@diabcare.com' },
            ],
        },
    };
}
