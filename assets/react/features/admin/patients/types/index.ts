export type DiabetesType = 'Type 1' | 'Type 2' | 'Gestationnel';

export interface Patient {
    id: string;
    nom: string;
    dateNaissance: string;
    typeDiabete: DiabetesType;
    equipeSoins: string;
    statut: 'Active' | 'Inactive';
}

export interface PatientsFilters {
    search: string;
    typeDiabete: DiabetesType | 'Tous';
}
