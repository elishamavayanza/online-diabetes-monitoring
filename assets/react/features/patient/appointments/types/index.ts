// types.ts
export type AppointmentStatus = 'Confirmé' | 'En attente' | 'Terminé' | 'Annulé' | 'Absent';

export interface PatientAppointment {
    id: string;
    date: string;
    heure: string;
    professionnel: string;
    motif: string;
    statut: AppointmentStatus;
}

export interface ProfessionalOption {
    id: string;
    fullName: string;
}
