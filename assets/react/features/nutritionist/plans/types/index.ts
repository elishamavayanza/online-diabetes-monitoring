export interface MealPlan {
    id: string;
    patient: string;
    titre: string;
    dateCreation: string;
    statut: 'Actif' | 'Archivé';
}
