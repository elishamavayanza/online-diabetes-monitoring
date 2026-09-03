export type TreatmentCategory = 'INSULINE' | 'COMPRIMÉ' | 'AUTRE';

export interface Treatment {
    id: string;
    categorie: TreatmentCategory;
    nom: string;
    dosage: string;
    horaires: string[];
    instructions?: string;        // ✅ nouveau
    quantity?: string;            // ✅ nouveau
    startDate?: string;           // ✅ nouveau (date de début de la prescription)
    endDate?: string;             // ✅ nouveau (date de fin)
    prescriberName?: string;      // ✅ nouveau si disponible
}

export interface TreatmentsData {
    treatments: Treatment[];       // actifs
    pastTreatments: Treatment[];   // terminés
}
