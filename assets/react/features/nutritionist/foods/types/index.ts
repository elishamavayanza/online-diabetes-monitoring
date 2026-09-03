export interface Food {
    id: string;
    categoryId: string;
    name: string;
    description?: string | null;
    photoUrl?: string | null;
    caloriesPer100g: string;
    carbsPer100g: string;
    proteinPer100g: string;
    fatPer100g: string;
    createdById?: string | null;
    createdAt: string;
    updatedAt?: string | null;
}

export interface FoodCategory {
    id: string;
    label: string;
    description?: string | null;
}

export interface FoodFormValues {
    categoryId: string;
    name: string;
    description: string;
    photoUrl: string;
    caloriesPer100g: string;
    carbsPer100g: string;
    proteinPer100g: string;
    fatPer100g: string;
}

export interface FoodFilters {
    search: string;
    categoryId: string;
}
