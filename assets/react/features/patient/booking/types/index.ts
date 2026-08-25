export interface ProfessionalOption {
    id: string;
    nom: string;
    specialite: string;
}

export interface BookingFormData {
    professionnelId: string;
    date: string;
    heure: string;
    motif: string;
}

export interface AvailableSlot {
    time: string;
    disponible: boolean;
}
