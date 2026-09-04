// services/nutritionService.ts
import apiClient from '@/services/api/client';
import { ApiFeedback, unwrapApiData } from '@/react/utils/apiFeedback';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import {PatientMeal, PatientMealItem, NutritionData, FoodOption, MealPlan} from '../types';

// Récupère tous les repas et leurs éléments pour le patient connecté
export async function fetchNutrition(): Promise<NutritionData> {
    const patientId = getCurrentUserIdFromToken();
    if (!patientId) throw new Error('Utilisateur non identifié.');

    const [mealsResp, itemsResp] = await Promise.all([
        apiClient.get<ApiFeedback<PatientMeal[]>>(`/meals?patientId=${patientId}`),
        apiClient.get<ApiFeedback<PatientMealItem[]>>(`/meal-items/patient/${patientId}`),
    ]);

    const meals = unwrapApiData(mealsResp.data, 'Erreur lors du chargement des repas.');
    const allItems = unwrapApiData(itemsResp.data, 'Erreur lors du chargement des éléments de repas.');

    const mealItems: Record<string, PatientMealItem[]> = {};
    allItems.forEach((item) => {
        if (!mealItems[item.mealId]) mealItems[item.mealId] = [];
        mealItems[item.mealId].push(item);
    });

    return { meals, mealItems };
}

// Récupère la liste des aliments disponibles
export async function fetchFoods(): Promise<FoodOption[]> {
    const response = await apiClient.get<ApiFeedback<any[]>>('/foods');
    const foods = unwrapApiData(response.data, 'Erreur lors du chargement des aliments.');
    return foods.map((f) => ({
        id: String(f.id),
        name: f.name,
        photoUrl: f.photoUrl ?? '', // ✅ si le backend renvoie ce champ
        category: f.category ?? '',
    }));
}


// Crée un nouveau repas
export async function createMeal(data: {
    name: string;
    description?: string;
    mealType: string;
    patientId?: number;
}): Promise<PatientMeal> {
    const response = await apiClient.post<ApiFeedback<PatientMeal>>('/meals', data);
    return unwrapApiData(response.data, 'Erreur lors de la création du repas.');
}

// Supprime un repas
export async function deleteMeal(id: string): Promise<void> {
    const response = await apiClient.delete<ApiFeedback<unknown>>(`/meals/${id}`);
    unwrapApiData(response.data, 'Erreur lors de la suppression du repas.');
}

// Ajoute un aliment à un repas
export async function addMealItem(data: {
    mealId: string;
    foodId: string;
    portionGrams: string;
    breadUnits?: string;
}): Promise<PatientMealItem> {
    const response = await apiClient.post<ApiFeedback<PatientMealItem>>('/meal-items', data);
    return unwrapApiData(response.data, "Erreur lors de l'ajout de l'aliment.");
}

// Supprime un élément de repas
export async function deleteMealItem(id: string): Promise<void> {
    const response = await apiClient.delete<ApiFeedback<unknown>>(`/meal-items/${id}`);
    unwrapApiData(response.data, "Erreur lors de la suppression de l'élément.");
}

export async function createMealPlan(mealIds: string[]): Promise<MealPlan> {
    const patientId = getCurrentUserIdFromToken();
    if (!patientId) throw new Error('Utilisateur non identifié.');

    // Simulation d'appel API – remplacez l'URL par votre endpoint réel
    const response = await apiClient.post<ApiFeedback<MealPlan>>('/meal-plans', {
        patientId: Number(patientId),
        mealIds,
    });
    return unwrapApiData(response.data, 'Erreur lors de la création du plan.');
}

