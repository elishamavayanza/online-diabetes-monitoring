import { AffectationData, OrganisationOption, FacilityOption, DepartmentOption } from '../types/affectation';

// Simule des listes pour les sélections
const organisations: OrganisationOption[] = [
    { id: 'org1', nom: 'Clinique A' },
    { id: 'org2', nom: 'Hôpital B' },
    { id: 'org3', nom: 'Réseau C' },
];

const facilities: FacilityOption[] = [
    { id: 'fac1', nom: 'Bâtiment principal', organisationId: 'org1' },
    { id: 'fac2', nom: 'Annexe', organisationId: 'org1' },
    { id: 'fac3', nom: 'Centre principal', organisationId: 'org2' },
];

const departments: DepartmentOption[] = [
    { id: 'dep1', nom: 'Diabétologie', facilityId: 'fac1' },
    { id: 'dep2', nom: 'Nutrition', facilityId: 'fac1' },
    { id: 'dep3', nom: 'Cardiologie', facilityId: 'fac3' },
];

export async function fetchOrganisationsOptions(): Promise<OrganisationOption[]> {
    await new Promise((resolve) => setTimeout(resolve, 300));
    return organisations;
}

export async function fetchFacilitiesByOrg(orgId: string): Promise<FacilityOption[]> {
    await new Promise((resolve) => setTimeout(resolve, 200));
    return facilities.filter((f) => f.organisationId === orgId);
}

export async function fetchDepartmentsByFacility(facId: string): Promise<DepartmentOption[]> {
    await new Promise((resolve) => setTimeout(resolve, 200));
    return departments.filter((d) => d.facilityId === facId);
}

export async function createAffectation(payload: AffectationData): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 800));
    console.log('Affectation créée', payload);
    // Appel API à implémenter
}

export async function updateAffectation(affectationId: string, payload: AffectationData): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 800));
    console.log('Affectation mise à jour', affectationId, payload);
    // Appel API à implémenter
}
