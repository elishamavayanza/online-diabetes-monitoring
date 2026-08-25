export interface CareTeamMember {
    id: string;
    nom: string;
    role: 'Clinician' | 'Nutritionniste' | 'Autre';
    specialite?: string;
    fonction: string;
}
