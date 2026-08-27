export type UserFormType = 'patient' | 'professional';

export interface BaseUserFormValues {
    email: string;
    password: string;
    fullName: string;
    phone: string;
    gender: string;
    locale: string;
    avatarUrl?: string;
    avatarFile?: File | null;
    address: {
        street: string;
        city: string;
        postalCode: string;
        country: string;
    };
}

export interface ProfessionalFormValues extends BaseUserFormValues {
    licenseNumber: string;
    professionalType: string;
    specialty: string;
    signatureUrl: string;
}

export interface PatientFormValues extends BaseUserFormValues {
    dateOfBirth: string;
    placeOfBirth: string;
    bloodType: string;
    heightCm: string;
}

export type UserFormValues = ProfessionalFormValues | PatientFormValues;
