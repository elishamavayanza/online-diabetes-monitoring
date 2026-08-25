export type AppointmentStatus = 'Confirmé' | 'En attente' | 'Terminé' | 'Annulé';

export interface PatientAppointment {
    id: string;
    date: string;
    heure: string;
    professionnel: string;
    motif: string;
    statut: AppointmentStatus;
}
