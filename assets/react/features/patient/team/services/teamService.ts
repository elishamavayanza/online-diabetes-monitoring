import { CareTeamMember } from '../types';

export async function fetchCareTeam(): Promise<CareTeamMember[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return [
        {
            id: '1',
            nom: 'Dr. Jean Dupont',
            role: 'Clinician',
            specialite: 'Endocrinologie',
            fonction: 'Médecin principal',
        },
        {
            id: '2',
            nom: 'Marie X',
            role: 'Nutritionniste',
            specialite: 'Nutrition',
            fonction: 'Nutrition',
        },
    ];
}
