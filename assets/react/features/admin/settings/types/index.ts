export type OrganizationStatus = 'Active' | 'Inactive';

export interface OrganizationSettings {
    nomCourt: string;
    type: string;
    logoUrl?: string;
    email: string;
    telephone: string;
    siteWeb: string;
    adresse: string;
    statut: OrganizationStatus;
}
