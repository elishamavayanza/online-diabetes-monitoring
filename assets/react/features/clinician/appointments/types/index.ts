export type AppointmentStatus = 'today' | 'upcoming' | 'completed' | 'cancelled';

// types.ts
export interface Appointment {
    id: string;
    patientId: string;   // identifiant technique
    patient: string;     // nom complet affiché
    date: string;
    heure: string;
    motif: string;
    statut: 'Confirmé' | 'Terminé' | 'Annulé' | 'En attente';
}

export type AppointmentFilter = 'today' | 'upcoming' | 'completed' | 'cancelled';
