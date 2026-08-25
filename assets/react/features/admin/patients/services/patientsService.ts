import { Patient, PatientsFilters } from '../types';

export async function fetchPatients(filters: PatientsFilters): Promise<Patient[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    const allPatients: Patient[] = [
        {
            id: '1',
            nom: 'Marie Zawadi',
            dateNaissance: '1995-06-15',
            typeDiabete: 'Type 1',
            equipeSoins: 'Équipe Diabétologie',
            statut: 'Active',
        },
        {
            id: '2',
            nom: 'Jean-Pierre L.',
            dateNaissance: '1980-03-22',
            typeDiabete: 'Type 2',
            equipeSoins: 'Équipe Nutrition',
            statut: 'Active',
        },
        {
            id: '3',
            nom: 'Alice M.',
            dateNaissance: '1975-11-30',
            typeDiabete: 'Type 2',
            equipeSoins: 'Équipe Médecine générale',
            statut: 'Inactive',
        },
    ];

    return allPatients.filter((patient) => {
        const matchesSearch = patient.nom.toLowerCase().includes(filters.search.toLowerCase());
        const matchesType = filters.typeDiabete === 'Tous' || patient.typeDiabete === filters.typeDiabete;
        return matchesSearch && matchesType;
    });
}
