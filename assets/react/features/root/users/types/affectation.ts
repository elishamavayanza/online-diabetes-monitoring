export interface OrganisationOption {
    id: string;
    nom: string;
}

export interface FacilityOption {
    id: string;
    nom: string;
    organisationId: string;
}

export interface DepartmentOption {
    id: string;
    nom: string;
    facilityId: string;
}

export interface AffectationFormValues {
    userId: string;
    organizationId: string;
    facilityId: string;
    departmentId: string;
    startDate: string;
    endDate: string;
    status: 'ACTIVE' | 'SUSPENDED' | 'ENDED';
}

export interface AffectationData {
    affectationId?: string;
    userId: string;
    organizationId: string;
    facilityId?: string;
    departmentId?: string;
    startDate: string;
    endDate?: string;
    status: string;
}
