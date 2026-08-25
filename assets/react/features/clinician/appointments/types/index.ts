export type AppointmentStatus = 'today' | 'upcoming' | 'completed' | 'cancelled';

export interface Appointment {
    id: string;
    patient: string;
    date: string;
    heure: string;
    motif: string;
    statut: 'Confirmé' | 'Terminé' | 'Annulé' | 'En attente';
}

export type AppointmentFilter = 'today' | 'upcoming' | 'completed' | 'cancelled';
