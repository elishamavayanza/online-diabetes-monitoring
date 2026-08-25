import { Professional } from '../types';

export async function fetchProfessionals(): Promise<Professional[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return [
        {
            id: '1',
            nom: 'Dr. Jean Dupont',
            type: 'Clinician',
            specialite: 'Endocrinologie',
            etablissement: 'Hôpital Central',
            departement: 'Diabétologie',
            statut: 'Active',
        },
        {
            id: '2',
            nom: 'Nutritionniste Sarah K.',
            type: 'Nutritionist',
            specialite: 'Nutrition clinique',
            etablissement: 'Hôpital Central',
            departement: 'Nutrition',
            statut: 'Active',
        },
        {
            id: '3',
            nom: 'Dr. Alice Martin',
            type: 'Clinician',
            specialite: 'Médecine générale',
            etablissement: 'Clinique du Lac',
            departement: 'Médecine générale',
            statut: 'Inactive',
        },
    ];
}
