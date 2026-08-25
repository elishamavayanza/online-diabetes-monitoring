export type NotificationType =
    | 'NEW_APPOINTMENT'
    | 'PLAN_UPDATED'
    | 'NEW_MESSAGE'
    | 'APPOINTMENT_SOON'
    | 'PATIENT_ADDED';

export interface NutritionistNotification {
    id: string;
    titre: string;
    message: string;
    type: NotificationType;
    estLue: boolean;
    date: string;
}

export type NotificationFilter = 'Toutes' | 'Non lues';
