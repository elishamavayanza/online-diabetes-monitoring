export interface Department {
    id: string;
    nom: string;
    etablissement: string;
    specialite: string;
    personnel: number;
    statut: 'Active' | 'Inactive';
}
