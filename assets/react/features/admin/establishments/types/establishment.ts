// Types communs pour la page Établissements

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

// Type Department partagé (utilisé par le service des départements)
export interface Department {
    id: string;
    nom: string;
    etablissement: string;   // nom de l'établissement de rattachement
    specialite: string;
    personnel: number;
    statut: 'Active' | 'Inactive';
}
