export type ClinicianNotificationType =
    | 'PRESCRIPTION_UPDATED'
    | 'NEW_APPOINTMENT'
    | 'APPOINTMENT_IN_30_MIN'
    | 'NEW_MESSAGE'
    | 'PATIENT_ADDED_TO_TEAM';

export interface ClinicianNotification {
    id: string;
    titre: string;
    message: string;
    type: ClinicianNotificationType;
    estLue: boolean;
    date: string;
}

export type ClinicianNotificationFilter = 'Toutes' | 'Non lues';
