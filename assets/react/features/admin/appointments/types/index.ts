export type AppointmentStatus = 'Pending' | 'Confirmed' | 'Cancelled';

export interface Appointment {
    id: string;
    patient: string;
    professionnel: string;
    etablissement: string;
    date: string;
    heure: string;
    statut: AppointmentStatus;
}

export type AppointmentPeriod = 'today' | 'week' | 'month';
