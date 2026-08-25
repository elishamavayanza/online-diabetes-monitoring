import { Department } from '../types';

export async function fetchDepartments(): Promise<Department[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return [
        { id: '1', nom: 'Diabétologie', etablissement: 'Hôpital Central', specialite: 'Endocrinologie', personnel: 8, statut: 'Active' },
        { id: '2', nom: 'Nutrition', etablissement: 'Hôpital Central', specialite: 'Nutrition clinique', personnel: 5, statut: 'Active' },
        { id: '3', nom: 'Médecine générale', etablissement: 'Clinique du Lac', specialite: 'Médecine générale', personnel: 12, statut: 'Active' },
        { id: '4', nom: 'Cardiologie', etablissement: 'Hôpital Central', specialite: 'Cardiologie', personnel: 6, statut: 'Inactive' },
        { id: '5', nom: 'Laboratoire', etablissement: 'Centre Médical Espoir', specialite: 'Biologie médicale', personnel: 4, statut: 'Active' },
    ];
}
