export type MedicationClass = 'INSULIN' | 'GENERAL';

export type MedicationForm = 'TABLET' | 'LIQUID';

export interface Medication {
    id: string;
    name: string;
    category: MedicationClass;
    form?: MedicationForm | null;
    description?: string;
    manufacturer?: string;
    insulinType?: string | null;
    concentration?: string | null;
    active: boolean;
    createdAt?: string;
    updatedAt?: string;
}

export interface MedicationFormValues {
    name: string;
    category: MedicationClass;
    form?: MedicationForm | null;
    description?: string;
    manufacturer?: string;
    insulinType?: string | null;
    concentration?: string | null;
    active?: boolean;
}

export interface MedicationFilters {
    search: string;
    active: 'all' | 'active' | 'inactive';
}