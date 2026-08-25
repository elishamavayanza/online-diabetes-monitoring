export type AppointmentStatus = 'Confirmé' | 'En attente' | 'Terminé' | 'Annulé';

export interface Appointment {
    id: string;
    patient: string;
    date: string;
    heure: string;
    motif: string;
    statut: AppointmentStatus;
}

export type AppointmentFilter = 'today' | 'upcoming' | 'completed' | 'cancelled';
