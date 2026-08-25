import { TreatmentsData } from '../types';

export async function fetchTreatments(): Promise<TreatmentsData> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return {
        treatments: [
            {
                id: '1',
                categorie: 'INSULINE',
                nom: 'Insuline X',
                dosage: '10 unités',
                horaires: ['Matin', 'Soir'],
            },
            {
                id: '2',
                categorie: 'COMPRIMÉ',
                nom: 'Metformine',
                dosage: '500 mg',
                horaires: ['Matin', 'Midi', 'Soir'],
            },
        ],
    };
}
