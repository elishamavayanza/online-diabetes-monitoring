import { useEffect, useState } from 'react';
import { fetchMealPlans } from '../services/mealPlansService';
import { MealPlan } from '../types';

export function useMealPlans() {
    const [plans, setPlans] = useState<MealPlan[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchMealPlans();
                setPlans(data);
            } catch (err) {
                setError('Impossible de charger les plans alimentaires.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { plans, isLoading, error };
}
