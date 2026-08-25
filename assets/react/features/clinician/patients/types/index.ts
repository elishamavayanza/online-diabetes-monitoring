export interface ClinicianPatient {
    id: string;
    nom: string;
    derniereConsultation: string;
    prochainRendezVous: string;
    statut: 'Active' | 'Inactive';
}
