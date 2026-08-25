export type AdminNotificationType = 'SYSTEM_ALERT' | 'MESSAGE_RECEIVED' | 'APPOINTMENT_CREATED';

export interface AdminNotification {
    id: string;
    titre: string;
    message: string;
    type: AdminNotificationType;
    estLue: boolean;
    date: string;
}

export type AdminNotificationFilter = 'Toutes' | 'Non lues' | 'Système';
