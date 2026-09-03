import { useCallback, useEffect, useState } from 'react';
import { deleteFood, fetchFoodCategories, fetchFoods } from '../services/foodsService';
import { Food, FoodCategory, FoodFilters } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function useFoods() {
    const { showToast } = useToast();
    const [allFoods, setAllFoods] = useState<Food[]>([]);
    const [foods, setFoods] = useState<Food[]>([]);
    const [categories, setCategories] = useState<FoodCategory[]>([]);
    const [filters, setFilters] = useState<FoodFilters>({ search: '', categoryId: '' });
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const [foodsData, categoriesData] = await Promise.all([
                fetchFoods(),
                fetchFoodCategories(),
            ]);
            setAllFoods(foodsData);
            setCategories(categoriesData);
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Impossible de charger les aliments.';
            setError(message);
            showToast({ type: 'error', message });
        } finally {
            setIsLoading(false);
        }
    }, [showToast]);

    useEffect(() => {
        load();
    }, [load]);

    useEffect(() => {
        const term = filters.search.toLowerCase();
        const filtered = allFoods.filter((food) => {
            const matchesSearch =
                !term ||
                food.name.toLowerCase().includes(term) ||
                (food.description ?? '').toLowerCase().includes(term);
            const matchesCategory = !filters.categoryId || food.categoryId === filters.categoryId;
            return matchesSearch && matchesCategory;
        });
        setFoods(filtered);
    }, [allFoods, filters]);

    const removeFood = async (id: string): Promise<boolean> => {
        try {
            await deleteFood(id);
            showToast({ type: 'success', message: 'Aliment supprimé avec succès.' });
            await load();
            return true;
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erreur lors de la suppression.';
            showToast({ type: 'error', message });
            return false;
        }
    };

    return {
        foods,
        categories,
        filters,
        setFilters,
        isLoading,
        error,
        refetch: load,
        removeFood,
    };
}
