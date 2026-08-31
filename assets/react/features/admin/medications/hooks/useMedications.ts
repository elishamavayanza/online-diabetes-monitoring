import { useCallback, useEffect, useState } from 'react';
import { fetchMedications } from '../services/medicationsService';
import { Medication, MedicationFilters } from '../types/types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function useMedications() {
    const [allMedications, setAllMedications] = useState<Medication[]>([]);
    const [medications, setMedications] = useState<Medication[]>([]);
    const [filters, setFilters] = useState<MedicationFilters>({ search: '', active: 'all' });
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const { showToast } = useToast();

    const load = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchMedications(); // plus de filtres envoyés
            setAllMedications(data);
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Impossible de charger les médicaments.';
            setError(message);
            showToast({ type: 'error', message });
        } finally {
            setIsLoading(false);
        }
    }, [showToast]);

    useEffect(() => {
        load();
    }, [load]);

    // Filtrage local selon les filtres
    useEffect(() => {
        const filtered = allMedications.filter((med) => {
            const matchesSearch = med.name.toLowerCase().includes(filters.search.toLowerCase());
            const matchesActive =
                filters.active === 'all' ||
                (filters.active === 'active' && med.active) ||
                (filters.active === 'inactive' && !med.active);
            return matchesSearch && matchesActive;
        });
        setMedications(filtered);
    }, [allMedications, filters]);

    return { medications, filters, setFilters, isLoading, error, refetch: load };
}
