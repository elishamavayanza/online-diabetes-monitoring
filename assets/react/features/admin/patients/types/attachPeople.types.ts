export type CareTeamRole = 'PRIMARY_CLINICIAN' | 'SPECIALIST' | 'NUTRITIONIST';

export interface CareTeamAssignmentItem {
    id: string; // identifiant unique local pour React (ex: uuid)
    professionalId: string;
    role: CareTeamRole;
    startDate: string;
    endDate: string;
    active: boolean;
}

export interface AttachPeopleFormValues {
    patientId: string;
    assignments: CareTeamAssignmentItem[];
}

export interface ProfessionalOption {
    id: string;
    nom: string;
    specialty: string;
}
