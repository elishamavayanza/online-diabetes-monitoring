export type ProfessionalType = 'Clinician' | 'Nutritionist';
export type CareTeamRole = 'PRIMARY_CLINICIAN' | 'SPECIALIST' | 'NUTRITIONIST';

export interface Professional {
    id: string;
    nom: string;
    type: ProfessionalType;
    specialite: string;
    etablissement: string;
    departement: string;
    statut: 'Active' | 'Inactive';
    avatarUrl?: string;
    email?: string;
}

export interface CareTeamAssignmentFormValues {
    patientId: string;
    professionalId: string;
    role: CareTeamRole;
    startDate: string;
    endDate: string;
    active: boolean;
}
