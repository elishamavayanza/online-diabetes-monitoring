import { useEffect, useState } from 'react';
import { fetchFoods } from '../services/foodsService';
import { Food } from '../types';

export function useFoods() {
    const [foods, setFoods] = useState<Food[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchFoods();
                setFoods(data);
            } catch (err) {
                setError('Impossible de charger les aliments.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { foods, isLoading, error };
}
