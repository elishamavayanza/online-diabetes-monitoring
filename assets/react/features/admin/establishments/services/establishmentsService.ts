import { EstablishmentsData } from '../types';

export async function fetchEstablishments(): Promise<EstablishmentsData> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return {
        establishments: [
            {
                id: '1',
                nom: 'Hôpital Central',
                adresse: '12 Avenue de la Paix, Goma',
                telephone: '+243 990 000 001',
                statut: 'Active',
                departementsCount: 5,
            },
            {
                id: '2',
                nom: 'Clinique du Lac',
                adresse: '45 Boulevard Kanyamuhanga, Goma',
                telephone: '+243 990 000 002',
                statut: 'Active',
                departementsCount: 3,
            },
            {
                id: '3',
                nom: 'Centre Médical Espoir',
                adresse: '78 Rue du Marché, Bukavu',
                telephone: '+243 990 000 003',
                statut: 'Inactive',
                departementsCount: 2,
            },
        ],
    };
}
