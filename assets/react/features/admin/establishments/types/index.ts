export interface Establishment {
    id: string;
    nom: string;
    adresse: string;
    telephone: string;
    statut: 'Active' | 'Inactive';
    departementsCount: number;
}

export interface EstablishmentsData {
    establishments: Establishment[];
}
