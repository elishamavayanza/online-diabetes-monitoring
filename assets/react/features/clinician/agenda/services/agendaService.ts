import { AgendaData } from '../types';

export async function fetchAgenda(): Promise<AgendaData> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return {
        days: [
            {
                date: '2026-08-24',
                label: 'Lun 24',
                appointments: [
                    { id: '1', time: '08:00', patient: 'Jean Dupont', motif: 'Consultation', type: 'Consultation' },
                    { id: '2', time: '10:30', patient: 'Marie X', motif: 'Suivi diabète', type: 'Suivi diabète' },
                ],
            },
            {
                date: '2026-08-25',
                label: 'Mar 25',
                appointments: [
                    { id: '3', time: '09:00', patient: 'Patient A', motif: 'Consultation', type: 'Consultation' },
                ],
            },
            {
                date: '2026-08-26',
                label: 'Mer 26',
                appointments: [
                    { id: '4', time: '14:00', patient: 'Patient B', motif: 'Suivi diabète', type: 'Suivi diabète' },
                ],
            },
            {
                date: '2026-08-27',
                label: 'Jeu 27',
                appointments: [],
            },
            {
                date: '2026-08-28',
                label: 'Ven 28',
                appointments: [
                    { id: '5', time: '11:00', patient: 'Patient C', motif: 'Consultation', type: 'Consultation' },
                ],
            },
        ],
    };
}
