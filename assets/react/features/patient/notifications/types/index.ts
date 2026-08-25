export type PatientNotificationType =
    | 'MEDICATION_REMINDER'
    | 'APPOINTMENT'
    | 'NEW_MESSAGE'
    | 'PRESCRIPTION_UPDATED'
    | 'MEASUREMENT_REMINDER';

export interface PatientNotification {
    id: string;
    titre: string;
    message: string;
    type: PatientNotificationType;
    estLue: boolean;
    date: string;
}

export type PatientNotificationFilter = 'Toutes' | 'Non lues';
