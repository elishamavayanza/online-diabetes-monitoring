export type UserType = 'Professional' | 'Patient' | 'Administrator';

export interface User {
    id: string;
    nom: string;
    email: string;
    type: UserType;
    organisation: string | null;
    statut: 'Active' | 'Inactive' | 'Pending';
    derniereConnexion: string;
    avatarUrl?: string;
}

export interface UsersFilters {
    type: 'Tous' | 'Professionnels' | 'Patients' | 'Administrateurs';
}
