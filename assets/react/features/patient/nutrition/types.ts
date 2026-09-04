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
    foodName?: string;
    portionGrams: string;
    breadUnits?: string;
    createdAt?: string;
    updatedAt?: string;
}

export interface NutritionData {
    meals: PatientMeal[];
    mealItems: Record<string, PatientMealItem[]>;
}

export interface FoodOption {
    id: string;
    name: string;
    photoUrl?: string; // ✅ ajout
    category?: string;
    calories?: number;
    // autres champs nutritionnels
}

export interface MealPlan {
    id: string;
    patientId: number;
    mealIds: string[];
    createdAt: string;
}
