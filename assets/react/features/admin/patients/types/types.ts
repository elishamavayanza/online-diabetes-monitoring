export interface AddressForm {
    street: string;
    city: string;
    postalCode: string;
    country: string;
}

export interface PatientFormValues {
    email: string;
    password: string;
    fullName: string;
    phone: string;
    gender: string;
    locale: string;
    avatarUrl?: string;
    avatarFile?: File | null;
    address: AddressForm;
    dateOfBirth: string;
    placeOfBirth: string;
    bloodType: string;
    heightCm: string;
}
