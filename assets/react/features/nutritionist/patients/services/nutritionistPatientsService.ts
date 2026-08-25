import { NutritionistPatient } from '../types';

export async function fetchNutritionistPatients(search: string): Promise<NutritionistPatient[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    const all: NutritionistPatient[] = [
        { id: '1', nom: 'Marie Zawadi', dernierPlan: 'Plan diabète n°3', prochainRendezVous: '2026-08-28 09:30', statut: 'Actif' },
        { id: '2', nom: 'Jean-Pierre L.', dernierPlan: 'Plan pauvre en sucres', prochainRendezVous: '2026-08-27 11:00', statut: 'Actif' },
        { id: '3', nom: 'Alice M.', dernierPlan: null, prochainRendezVous: '—', statut: 'Inactif' },
    ];

    if (!search) return all;
    return all.filter((p) => p.nom.toLowerCase().includes(search.toLowerCase()));
}
