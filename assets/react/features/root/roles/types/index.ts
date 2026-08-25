export type RoleId = 'ROOT' | 'ADMIN' | 'CLINICIAN' | 'NUTRITIONIST' | 'PATIENT';

export interface RoleDetails {
    id: RoleId;
    label: string;
    permissions: string[];
}

export interface UserSummary {
    id: string;
    nom: string;
    email: string;
}

export interface RoleData {
    roles: RoleDetails[];
    usersByRole: Record<RoleId, UserSummary[]>;
}
