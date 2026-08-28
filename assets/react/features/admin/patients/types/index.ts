export type DiabetesType = 'Type 1' | 'Type 2' | 'Gestationnel';

export interface Patient {
    id: string;
    nom: string;
    dateNaissance: string;
    typeDiabete: 'Type 1' | 'Type 2' | 'Gestationnel';
    equipeSoins: string;
    statut: 'Active' | 'Inactive';
    avatarUrl?: string;    // ✅ photo de profil
    email?: string;        // ✅ e-mail
    telephone?: string;    // ✅ téléphone
}

export interface PatientsFilters {
    search: string;
    typeDiabete: DiabetesType | 'Tous';
}
