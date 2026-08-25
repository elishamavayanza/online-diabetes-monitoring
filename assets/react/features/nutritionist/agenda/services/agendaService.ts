import { AgendaData } from '../types';

export async function fetchAgenda(): Promise<AgendaData> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return {
        days: [
            {
                date: '2026-08-24',
                label: 'Lundi',
                appointments: [
                    { id: '1', time: '08:00', patient: 'Jean Dupont', motif: 'Suivi nutritionnel' },
                    { id: '2', time: '10:00', patient: 'Marie X', motif: 'Première consultation' },
                ],
            },
            {
                date: '2026-08-25',
                label: 'Mardi',
                appointments: [
                    { id: '3', time: '09:00', patient: 'Patient Y', motif: 'Ajustement plan' },
                ],
            },
            {
                date: '2026-08-26',
                label: 'Mercredi',
                appointments: [],
            },
            {
                date: '2026-08-27',
                label: 'Jeudi',
                appointments: [
                    { id: '4', time: '14:00', patient: 'Alice M.', motif: 'Suivi' },
                ],
            },
            {
                date: '2026-08-28',
                label: 'Vendredi',
                appointments: [],
            },
        ],
    };
}
