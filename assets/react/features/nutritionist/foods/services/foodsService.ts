import { Food } from '../types';

export async function fetchFoods(): Promise<Food[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));
    return [
        { id: '1', nom: 'Riz blanc', categorie: 'Céréales', glucides: 28, calories: 130 },
        { id: '2', nom: 'Poulet grillé', categorie: 'Protéines', glucides: 0, calories: 165 },
        { id: '3', nom: 'Brocoli', categorie: 'Légumes', glucides: 7, calories: 34 },
    ];
}
