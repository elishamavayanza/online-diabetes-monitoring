export interface ClinicianPatient {
    id: string;
    nom: string;
    derniereConsultation?: string;
    prochainRendezVous?: string;
    statut: 'Active' | 'Inactive';
    //  nouveaux champs
    avatarUrl?: string;
    dateNaissance?: string;
    telephone?: string;

    hasMedicalRecord?: boolean
    medicalRecordStatus: 'none' | 'closed' | 'open';

}

export interface MedicalRecord {
    id: string;
    patientId: string;
    status: 'open' | 'closed' | 'none';
    // Contenu du dossier (exemple)
    heightCm?: number;
    weightKg?: number;
    bloodType?: string;
    allergies?: string[];
    diagnoses?: string[];
    // ... autres champs
    createdAt?: string;
    updatedAt?: string;
}
