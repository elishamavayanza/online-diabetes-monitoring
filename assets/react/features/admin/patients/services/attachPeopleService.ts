import {AttachPeopleFormValues, CareTeamAssignmentItem, ProfessionalOption} from '../types/attachPeople.types';

export async function attachProfessionalsToPatient(
    payload: AttachPeopleFormValues
): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 800));
    console.log('Affectations multiples créées', payload);
    // Appel API à implémenter
}

export async function fetchProfessionalsForAttach(): Promise<ProfessionalOption[]> {
    await new Promise((resolve) => setTimeout(resolve, 400));
    // Simulation de professionnels disponibles
    return [
        { id: 'prof1', nom: 'Dr. Jean Dupont', specialty: 'Endocrinologie' },
        { id: 'prof2', nom: 'Nutritionniste Sarah', specialty: 'Nutrition' },
        { id: 'prof3', nom: 'Dr. Alice Martin', specialty: 'Médecine générale' },
    ];
}

export async function fetchExistingAssignments(
    patientId: string
): Promise<CareTeamAssignmentItem[]> {
    await new Promise((resolve) => setTimeout(resolve, 400));
    // Simulation : affectations existantes
    return [
        {
            id: 'existing-1',
            professionalId: 'prof1',
            role: 'PRIMARY_CLINICIAN',
            startDate: '2026-01-01',
            endDate: '',
            active: true,
        },
        {
            id: 'existing-2',
            professionalId: 'prof2',
            role: 'NUTRITIONIST',
            startDate: '2026-02-15',
            endDate: '',
            active: true,
        },
    ];
}

export async function updateAssignmentsForPatient(
    patientId: string,
    payload: AttachPeopleFormValues
): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 800));
    console.log('Affectations mises à jour', patientId, payload);
    // Appel API
}
