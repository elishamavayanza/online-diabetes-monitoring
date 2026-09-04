// hooks/useNutrition.ts
import { useEffect, useState, useCallback } from 'react';
import {
    fetchNutrition,
    createMeal,
    deleteMeal,
    addMealItem,
    deleteMealItem,
} from '../services/nutritionService';
import { PatientMeal, PatientMealItem } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import {getCurrentUserIdFromToken} from "@/react/utils/authUtils";

export function useNutrition() {
    const { showToast } = useToast();
    const [meals, setMeals] = useState<PatientMeal[]>([]);
    const [mealItems, setMealItems] = useState<Record<string, PatientMealItem[]>>({});
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

    return { meals, mealItems, isLoading, error, addMeal, removeMeal, addItem, removeItem, reload: load };
}
