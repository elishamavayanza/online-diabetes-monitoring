import { Member } from '../types';

export async function fetchMembers(): Promise<Member[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return [
        {
            id: '1',
            nom: 'Jean Dupont',
            role: 'Clinician',
            etablissement: 'Hôpital Central',
            departement: 'Diabétologie',
            statut: 'Active',
            dateArrivee: '2025-01-15',
        },
        {
            id: '2',
            nom: 'Marie X',
            role: 'Administrateur',
            etablissement: 'Clinique A',
            departement: null,
            statut: 'Active',
            dateArrivee: '2024-11-01',
        },
        {
            id: '3',
            nom: 'Paul K.',
            role: 'Nutritionist',
            etablissement: 'Hôpital Central',
            departement: 'Nutrition',
            statut: 'Inactive',
            dateArrivee: '2026-02-20',
        },
    ];
}
