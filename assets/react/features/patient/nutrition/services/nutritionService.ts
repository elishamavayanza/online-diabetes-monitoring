// services/nutritionService.ts
import apiClient from '@/services/api/client';
import { ApiFeedback, unwrapApiData } from '@/react/utils/apiFeedback';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import {PatientMeal, PatientMealItem, NutritionData, FoodOption, MealPlan} from '../types';

let foodCategoriesPromise: Promise<{ id: string; label: string }[]> | null = null;

// Récupère tous les repas (items nestés) pour le patient connecté — 1 requête au lieu de 2
export async function fetchNutrition(): Promise<NutritionData> {
    const patientId = getCurrentUserIdFromToken();
    if (!patientId) throw new Error('Utilisateur non identifié.');

    const mealsResp = await apiClient.get<ApiFeedback<(PatientMeal & { items?: PatientMealItem[] })[]>>(
        `/meals?patientId=${patientId}&includeItems=true`,
    );

    const meals = unwrapApiData(mealsResp.data, 'Erreur lors du chargement des repas.');

    const mealItems: Record<string, PatientMealItem[]> = {};
    meals.forEach((meal) => {
        const items = Array.isArray(meal.items) ? meal.items : [];
        mealItems[meal.id] = items;
    });

    return { meals, mealItems };
}

// Récupère la liste des aliments disponibles
export async function fetchFoods(): Promise<FoodOption[]> {
    const response = await apiClient.get<ApiFeedback<any[]>>('/foods');
    const foods = unwrapApiData(response.data, 'Erreur lors du chargement des aliments.');

    // Récupère les catégories une seule fois (promise partagée anti-doublon)
    const categories = await fetchFoodCategories();
    const categoryMap = new Map(categories.map((c) => [c.id, c.label]));

    return foods.map((f) => ({
        id: String(f.id),
        name: f.name,
        photoUrl: f.photoUrl ?? '',
        category: f.categoryId
            ? (categoryMap.get(String(f.categoryId)) ?? '')
            : (f.category ?? ''),
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

    const response = await apiClient.post<ApiFeedback<MealPlan>>('/meal-plans', {
        patientId: Number(patientId),
        mealIds,
    });
    return unwrapApiData(response.data, 'Erreur lors de la création du plan.');
}

// Récupère les catégories d'aliments avec leur ID et libellé (dédupliquée en vol)
export async function fetchFoodCategories(): Promise<{ id: string; label: string }[]> {
    if (!foodCategoriesPromise) {
        foodCategoriesPromise = (async () => {
            const response = await apiClient.get<ApiFeedback<any[]>>('/food-categories');
            const categories = unwrapApiData(response.data, 'Erreur lors du chargement des catégories.');
            return categories.map((c) => ({
                id: String(c.id),
                label: c.label ?? c.name ?? '',
            }));
        })().catch((err) => {
            foodCategoriesPromise = null;
            throw err;
        });
    }
    return foodCategoriesPromise;
}
