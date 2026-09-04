// hooks/useNutrition.ts
import { useEffect, useState, useCallback } from 'react';
import {
    fetchNutrition,
    createMeal,
    deleteMeal,
    addMealItem,
    deleteMealItem,
} from '../services/nutritionService';
import { PatientMeal, PatientMealItem, FoodOption, MealType } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';

export function useNutrition() {
    const { showToast } = useToast();
    const [meals, setMeals] = useState<PatientMeal[]>([]);
    const [mealItems, setMealItems] = useState<Record<string, PatientMealItem[]>>({});
    const [selectedFoods, setSelectedFoods] = useState<FoodOption[]>([]);
    const [selectedDate, setSelectedDate] = useState<Date>(new Date());
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchNutrition();
            setMeals(data.meals);
            setMealItems(data.mealItems);
        } catch (err) {
            setError('Impossible de charger les données de nutrition.');
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        load();
    }, [load]);

    // Filtrer les repas par date (basé sur createdAt)
    const mealsForDate = meals.filter((meal) => {
        if (!meal.createdAt) return false;
        const mealDate = new Date(meal.createdAt as string);
        return (
            mealDate.getFullYear() === selectedDate.getFullYear() &&
            mealDate.getMonth() === selectedDate.getMonth() &&
            mealDate.getDate() === selectedDate.getDate()
        );
    });

    // ✅ Correction : type guard pour s'assurer que createdAt est une string
    const markedDates = meals
        .filter((meal): meal is PatientMeal & { createdAt: string } => !!meal.createdAt)
        .map((meal) => {
            const d = new Date(meal.createdAt);
            return new Date(d.getFullYear(), d.getMonth(), d.getDate());
        })
        .filter(
            (date, index, self) =>
                self.findIndex((d) => d.toDateString() === date.toDateString()) === index
        )
        .map((date) => ({ date }));

    const addMeal = async (data: { name: string; description?: string; mealType: string }) => {
        try {
            await createMeal({
                ...data,
                patientId: Number(await getCurrentUserIdFromToken()) || undefined,
            });
            showToast({ type: 'success', message: 'Repas créé avec succès.' });
            await load();
        } catch (err) {
            showToast({ type: 'error', message: 'Erreur lors de la création du repas.' });
        }
    };

    const removeMeal = async (id: string) => {
        try {
            await deleteMeal(id);
            showToast({ type: 'success', message: 'Repas supprimé.' });
            await load();
        } catch (err) {
            showToast({ type: 'error', message: 'Erreur lors de la suppression du repas.' });
        }
    };

    const addItem = async (data: { mealId: string; foodId: string; portionGrams: string; breadUnits?: string }) => {
        try {
            await addMealItem(data);
            showToast({ type: 'success', message: 'Aliment ajouté au repas.' });
            await load();
        } catch (err) {
            showToast({ type: 'error', message: "Erreur lors de l'ajout de l'aliment." });
        }
    };

    const removeItem = async (id: string) => {
        try {
            await deleteMealItem(id);
            showToast({ type: 'success', message: 'Élément supprimé.' });
            await load();
        } catch (err) {
            showToast({ type: 'error', message: "Erreur lors de la suppression de l'élément." });
        }
    };

    // hooks/useNutrition.ts (extrait)
    const createPlan = async (data: {
        name: string;
        description?: string;
        mealType: string;
        items: { foodId: string; portionGrams: number; breadUnits?: number }[];
    }) => {
        try {
            const meal = await createMeal({
                name: data.name,
                description: data.description,
                mealType: data.mealType,
                patientId: Number(await getCurrentUserIdFromToken()) || undefined,
            });

            for (const item of data.items) {
                await addMealItem({
                    mealId: meal.id,
                    foodId: item.foodId,
                    portionGrams: String(item.portionGrams),
                    breadUnits: item.breadUnits !== undefined ? String(item.breadUnits) : undefined,
                });
            }

            showToast({ type: 'success', message: 'Plan repas créé avec succès.' });
            setSelectedFoods([]);
            await load();
        } catch (err) {
            showToast({ type: 'error', message: 'Erreur lors de la création du plan.' });
        }
    };

    return {
        meals: mealsForDate,
        allMeals: meals,
        markedDates,
        mealItems,
        selectedFoods,
        setSelectedFoods,
        selectedDate,
        setSelectedDate,
        isLoading,
        error,
        addMeal,
        removeMeal,
        addItem,
        removeItem,
        createPlan,
        reload: load,
    };
}
