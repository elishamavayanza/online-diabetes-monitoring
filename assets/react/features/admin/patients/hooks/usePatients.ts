import { useEffect, useState, useCallback } from 'react';
import { fetchPatients } from '../services/patientsService';
import { Patient, PatientsFilters } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function usePatients() {
    const [patients, setPatients] = useState<Patient[]>([]);
    const [filters, setFilters] = useState<PatientsFilters>({ search: '', typeDiabete: 'Tous' });
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const { showToast } = useToast();

    const load = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchPatients(filters);
            setPatients(data);
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Impossible de charger les patients.';
            setError(message);
            showToast({ type: 'error', message });
        } finally {
            setIsLoading(false);
        }
    }, [filters, showToast]);

    useEffect(() => {
        load();
    }, [load]);

    return { patients, filters, setFilters, isLoading, error, refetch: load };
}
