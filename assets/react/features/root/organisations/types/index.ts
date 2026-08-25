export interface Organisation {
    id: string;
    nom: string;
    type: 'Clinic' | 'Hospital' | 'Network';
    statut: 'Active' | 'Inactive';
}

export interface OrganisationsData {
    organisations: Organisation[];
}
