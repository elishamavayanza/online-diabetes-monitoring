import { OrganisationsData } from '../types';

export async function fetchOrganisations(): Promise<OrganisationsData> {
    // Simulation d'un délai réseau
    await new Promise((resolve) => setTimeout(resolve, 500));

    return {
        organisations: [
            { id: '1', nom: 'Clinique A', type: 'Clinic', statut: 'Active' },
            { id: '2', nom: 'Hôpital B', type: 'Hospital', statut: 'Active' },
            { id: '3', nom: 'Réseau C', type: 'Network', statut: 'Active' },
        ],
    };
}
