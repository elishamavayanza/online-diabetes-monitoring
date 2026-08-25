export type NotificationType = 'SYSTEM_ALERT' | 'MESSAGE_RECEIVED' | 'PRESCRIPTION_UPDATED';

export interface Notification {
    id: string;
    titre: string;
    message: string;
    type: NotificationType;
    estLue: boolean;
    date: string;
}

export type NotificationFilter = 'Toutes' | 'Non lues' | 'Alertes système';
