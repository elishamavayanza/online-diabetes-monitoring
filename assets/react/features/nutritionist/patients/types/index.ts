export interface NutritionistPatient {
    id: string;
    nom: string;
    dernierPlan: string | null;
    prochainRendezVous: string | null;
    statut: 'Actif' | 'Inactif';
}
