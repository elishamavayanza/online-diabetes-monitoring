import { MealPlan } from '../types';

export async function fetchMealPlans(): Promise<MealPlan[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));
    return [
        { id: '1', patient: 'Marie Zawadi', titre: 'Plan diabète n°3', dateCreation: '2026-08-10', statut: 'Actif' },
        { id: '2', patient: 'Jean-Pierre L.', titre: 'Plan pauvre en sucres', dateCreation: '2026-08-15', statut: 'Actif' },
        { id: '3', patient: 'Alice M.', titre: 'Plan équilibré', dateCreation: '2026-07-28', statut: 'Archivé' },
    ];
}
