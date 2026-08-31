export interface Medication {
    id: string;
    name: string;
    category: string;          // ex: 'INSULIN' | 'TABLET' | 'OTHER'
    description?: string;
    insulinLevel?: number;
    manufacturer?: string;
    active: boolean;           // si géré par l'entité
    createdAt?: string;
    updatedAt?: string;
}

export interface MedicationFormValues {
    name: string;
    category: string;
    description?: string;
    insulinLevel?: number;
    manufacturer?: string;
    active?: boolean;
}

export interface MedicationFilters {
    search: string;
    active: 'all' | 'active' | 'inactive';
}
