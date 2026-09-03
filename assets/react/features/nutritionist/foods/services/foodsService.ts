import apiClient from '@/services/api/client';
import { ApiFeedback, unwrapApiData } from '@/react/utils/apiFeedback';
import { Food, FoodCategory, FoodFormValues } from '../types';

function toPayload(values: FoodFormValues) {
    return {
        categoryId: values.categoryId,
        name: values.name.trim(),
        description: values.description.trim() || null,
        photoUrl: values.photoUrl.trim() || null,
        caloriesPer100g: formatDecimal(values.caloriesPer100g),
        carbsPer100g: formatDecimal(values.carbsPer100g),
        proteinPer100g: formatDecimal(values.proteinPer100g),
        fatPer100g: formatDecimal(values.fatPer100g),
    };
}

function formatDecimal(value: string): string {
    const num = Number(value);
    if (Number.isNaN(num)) return '0.00';
    return num.toFixed(2);
}

export async function fetchFoods(): Promise<Food[]> {
    const response = await apiClient.get<ApiFeedback<Food[]>>('/foods');
    return unwrapApiData(response.data, 'Impossible de charger les aliments.');
}

export async function fetchFoodCategories(): Promise<FoodCategory[]> {
    const response = await apiClient.get<ApiFeedback<FoodCategory[]>>('/food-categories');
    return unwrapApiData(response.data, 'Impossible de charger les catégories.');
}

export async function createFood(values: FoodFormValues): Promise<Food> {
    const response = await apiClient.post<ApiFeedback<Food>>('/foods', toPayload(values));
    return unwrapApiData(response.data, 'Erreur lors de la création de l\'aliment.');
}

export async function updateFood(id: string, values: FoodFormValues): Promise<Food> {
    const response = await apiClient.put<ApiFeedback<Food>>(`/foods/${id}`, toPayload(values));
    return unwrapApiData(response.data, 'Erreur lors de la mise à jour de l\'aliment.');
}

export async function deleteFood(id: string): Promise<void> {
    const response = await apiClient.delete<ApiFeedback<unknown>>(`/foods/${id}`);
    unwrapApiData(response.data, 'Erreur lors de la suppression de l\'aliment.');
}

export async function uploadFoodPhoto(file: File): Promise<string> {
    const formData = new FormData();
    formData.append('photo', file);

    const response = await apiClient.post<ApiFeedback<{ url: string }>>(
        '/foods/upload-photo',
        formData
    );


    const data = unwrapApiData(response.data, 'Erreur lors du téléversement de la photo.');
    return data.url;
}

export function foodToFormValues(food: Food): FoodFormValues {
    return {
        categoryId: food.categoryId,
        name: food.name,
        description: food.description ?? '',
        photoUrl: food.photoUrl ?? '',
        caloriesPer100g: food.caloriesPer100g,
        carbsPer100g: food.carbsPer100g,
        proteinPer100g: food.proteinPer100g,
        fatPer100g: food.fatPer100g,
    };
}

// services/foodsService.ts
export async function createFoodCategory(data: { label: string; description?: string }): Promise<FoodCategory> {
    const response = await apiClient.post<ApiFeedback<FoodCategory>>('/food-categories', {
        label: data.label,
        description: data.description || null,
    });
    return unwrapApiData(response.data, 'Erreur lors de la création de la catégorie.');
}
