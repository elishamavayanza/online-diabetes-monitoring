export interface Organisation {
    id: string;
    nom: string;
    type: 'Clinic' | 'Hospital' | 'Network';
    statut: 'Active' | 'Inactive';
}

export interface OrganisationsData {
    organisations: Organisation[];
}

export type OrganisationType = 'HOSPITAL' | 'CLINIC' | 'NETWORK';

export interface CreateOrganisationPayload {
    name: string;
    shortName: string;
    type: OrganisationType;
    email: string;
    phone: string;
    website: string;
    logoUrl: string;
    active: boolean;
    address?: {
        street: string;
        city: string;
        postalCode: string;
        country: string;
    };
}
