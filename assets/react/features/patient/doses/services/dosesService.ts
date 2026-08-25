import { DosesData } from '../types';

export async function fetchDoses(): Promise<DosesData> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return {
        today: [
            { id: '1', time: '08:00', medication: 'Insuline', statut: 'TAKEN' },
            { id: '2', time: '12:00', medication: 'Metformine', statut: 'TAKEN' },
            { id: '3', time: '18:00', medication: 'Insuline', statut: 'PENDING' },
        ],
    };
}
