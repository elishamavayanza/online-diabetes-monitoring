import { useEffect, useState } from 'react';
import { fetchDepartments } from '../services/departmentsService';
import { Department } from '../types';

export function useDepartments() {
    const [departments, setDepartments] = useState<Department[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchDepartments();
                setDepartments(data);
            } catch (err) {
                setError('Impossible de charger les départements.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { departments, isLoading, error };
}
