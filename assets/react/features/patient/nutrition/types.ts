// types.ts
export type MealType = 'BREAKFAST' | 'LUNCH' | 'DINNER' | 'SNACK';

export interface PatientMeal {
    id: string;
    name: string;
    description?: string;
    mealType: MealType;
    patientId?: number;
    createdAt?: string;
    updatedAt?: string;
}

export interface PatientMealItem {
    id: string;
    mealId: string;
    foodId: string;
    foodName?: string; // sera récupéré depuis la liste des aliments
    portionGrams: string;
    breadUnits?: string;
    createdAt?: string;
    updatedAt?: string;
}

export interface NutritionData {
    meals: PatientMeal[];
    mealItems: Record<string, PatientMealItem[]>; // groupé par mealId
}

export interface FoodOption {
    id: string;
    name: string;
}
