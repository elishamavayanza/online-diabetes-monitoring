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

// Portée d'une notification système
export type NotificationScope = 'USER' | 'ORGANIZATION' | 'GLOBAL';
export type NotificationChannel = 'PUSH' | 'EMAIL' | 'SMS' | 'IN_APP';

// Payload pour publier une notification système
export interface CreateSystemNotificationPayload {
    scope: NotificationScope;
    userId?: string;
    organizationId?: string;
    type: NotificationType;       // ✅ maintenant correct
    title: string;
    body: string;
    channel: NotificationChannel;
    relatedEntityType?: string;
    relatedEntityId?: string;
}
