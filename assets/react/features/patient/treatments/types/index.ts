// types.ts
export type TreatmentCategory = 'INSULINE' | 'COMPRIMÉ' | 'AUTRE';
// types.ts
export interface Treatment {
    id: string;
    prescriptionId: string;
    categorie: TreatmentCategory;
    nom: string;
    dosage: string;
    horaires: string[];
    instructions?: string;
    quantity?: string;
    startDate?: string;
    endDate?: string;
    prescriberName?: string;
    stopReason?: string;   // motif d'arrêt (notes backend)
    status?: string;       // statut de la prescription (COMPLETED, CANCELLED, etc.)
}

export interface TreatmentsData {
    treatments: Treatment[];          // actifs
    pastTreatments: Treatment[];      // terminés / arrêtés
}
