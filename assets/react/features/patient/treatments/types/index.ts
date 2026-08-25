export type TreatmentCategory = 'INSULINE' | 'COMPRIMÉ' | 'AUTRE';

export interface Treatment {
    id: string;
    categorie: TreatmentCategory;
    nom: string;
    dosage: string;
    horaires: string[];
}

export interface TreatmentsData {
    treatments: Treatment[];
}
