import { CareTeamAssignmentFormValues } from '../types/types';

export async function assignPatientToProfessional(
    payload: CareTeamAssignmentFormValues
): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 800));
    console.log('Affectation créée', payload);
    // Appel API à implémenter
}

export async function fetchPatientsForAssignment(): Promise<{ id: string; nom: string }[]> {
    await new Promise((resolve) => setTimeout(resolve, 400));
    // Simulation de patients non encore affectés à ce professionnel
    return [
        { id: 'p1', nom: 'Marie Zawadi' },
        { id: 'p2', nom: 'Jean-Pierre L.' },
        { id: 'p3', nom: 'Alice M.' },
    ];
}
