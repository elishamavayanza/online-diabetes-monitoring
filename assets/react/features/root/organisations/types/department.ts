export interface DepartmentFormValues {
    facilityId: string;      // ID de l'établissement parent
    name: string;
    specialty: string;
}

export interface Department extends DepartmentFormValues {
    id: string;
}
