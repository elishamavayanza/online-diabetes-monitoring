import { EstablishmentFormValues } from '../types/establishment';

export async function createEstablishment(payload: EstablishmentFormValues): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 800));
    console.log('Établissement créé', payload);
    // Appel API réel à implémenter
}

export async function updateEstablishment(id: string, payload: EstablishmentFormValues): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 800));
    console.log('Établissement mis à jour', id, payload);
    // Appel API réel à implémenter
}
