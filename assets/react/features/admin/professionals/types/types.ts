// features/admin/professionals/types.ts
export type ProfessionalType = 'Clinician' | 'Nutritionist';

export interface Professional {
    id: string;
    nom: string;
    type: ProfessionalType;
    specialite: string;
    etablissement: string;
    departement: string;
    statut: 'Active' | 'Inactive';
    avatarUrl?: string;
    email?: string;
}

export interface AddressForm {
    street: string;
    city: string;
    postalCode: string;
    country: string;
}

export interface ProfessionalFormValues {
    email: string;
    password: string;
    fullName: string;
    phone: string;
    gender: string;
    locale: string;
    avatarUrl?: string;
    avatarFile?: File | null;
    address: AddressForm;
    licenseNumber: string;
    professionalType: string;
    specialty: string;
    signatureUrl: string;
}
