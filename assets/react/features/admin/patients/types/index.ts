export type DiabetesType = 'TYPE_1' | 'TYPE_2' | 'GESTATIONAL' | 'OTHER';

export interface Patient {
    id: string;
    nom: string;
    dateNaissance: string;
    typeDiabete: DiabetesType; //  aligné sur l'enum backend
    equipeSoins: string;
    statut: 'Active' | 'Inactive';
    avatarUrl?: string;
    email?: string;
    telephone?: string;
}

export interface PatientsFilters {
    search: string;
    typeDiabete: DiabetesType | 'Tous';
}
