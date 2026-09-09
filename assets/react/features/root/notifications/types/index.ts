// Type de notification
export type NotificationType =
    | 'MEDICATION_REMINDER'
    | 'APPOINTMENT_REMINDER'
    | 'MEASUREMENT_REMINDER'
    | 'MESSAGE_RECEIVED'
    | 'PRESCRIPTION_UPDATED'
    | 'SYSTEM_ALERT';

// Interface d'une notification existante
export interface Notification {
    id: string;
    titre: string;
    message: string;
    type: NotificationType;
    estLue: boolean;
    date: string;
}

// Filtre pour la liste des notifications
export type NotificationFilter = 'Toutes' | 'Non lues' | 'Alertes système';

// Portée d'une notification système (niveau de publication)
export type NotificationScope = 'USER' | 'ORGANIZATION' | 'ROLE' | 'GLOBAL';
export type NotificationChannel = 'PUSH' | 'EMAIL' | 'SMS' | 'IN_APP';

// Payload pour publier une notification système
export interface CreateSystemNotificationPayload {
    scope: NotificationScope;
    userId?: string;
    organizationId?: string;
    role?: string;
    type: NotificationType;       // ✅ maintenant correct
    title: string;
    body: string;
    channel: NotificationChannel;
    relatedEntityType?: string;
    relatedEntityId?: string;
}

// Niveaux de publication par rôle
export const PUBLICATION_ROLES = [
    { value: 'ROLE_ROOT', label: 'Root (super admin)' },
    { value: 'ROLE_ADMIN', label: 'Administrateurs' },
    { value: 'ROLE_CLINICIAN', label: 'Cliniciens' },
    { value: 'ROLE_NUTRITIONIST', label: 'Nutritionnistes' },
    { value: 'ROLE_PATIENT', label: 'Patients' },
] as const;
