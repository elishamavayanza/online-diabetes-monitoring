// types.ts
export type AppointmentStatus =
    | 'En attente'       // SCHEDULED
    | 'Confirmé'         // CONFIRMED
    | 'Terminé'          // COMPLETED
    | 'Annulé'           // CANCELLED
    | 'Absent'           // NO_SHOW
    | 'Report demandé';  // RESCHEDULE_REQUESTED

export interface PatientAppointment {
    id: string;
    date: string;          // format affichable "jj/mm/aaaa"
    heure: string;         // format "hh:mm"
    professionnel: string;
    motif: string;
    statut: AppointmentStatus;
    scheduledAt: string;   // ✅ date ISO pour les comparaisons
}

export interface ProfessionalOption {
    id: string;
    fullName: string;
}
