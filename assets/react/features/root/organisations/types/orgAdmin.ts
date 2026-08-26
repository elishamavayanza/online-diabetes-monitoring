export type Gender = 'MALE' | 'FEMALE' | 'OTHER' | 'UNSPECIFIED';

export interface OrgAdminFormValues {
    email: string;
    password: string;
    fullName: string;
    gender: Gender;
    phone: string;
    locale: string;
    avatarUrl: string;
    address: {
        street: string;
        city: string;
        postalCode: string;
        country: string;
    };
}
