export interface Member {
    id: string;
    nom: string;
    role: string;
    etablissement: string | null;
    departement: string | null;
    statut: 'Active' | 'Inactive';
    dateArrivee: string;
}
