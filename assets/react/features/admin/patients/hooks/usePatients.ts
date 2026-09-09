import { useEffect, useState, useCallback } from 'react';
import { fetchPaginatedPatients } from '../services/patientsService';
import { Patient, PatientsFilters } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function usePatients() {
    const [patients, setPatients] = useState<Patient[]>([]);
    const [total, setTotal] = useState(0);
    const [totalPages, setTotalPages] = useState(1);
    const [page, setPage] = useState(1);
    const [limit] = useState(10);
    const [filters, setFilters] = useState<PatientsFilters>({ search: '', typeDiabete: 'Tous' });
    const [sort, setSort] = useState<string | null>(null);
    const [order, setOrder] = useState<'asc' | 'desc'>('desc');
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const { showToast } = useToast();

    const load = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchPaginatedPatients({
                q: filters.search,
                page,
                limit,
                sort: sort ?? undefined,
                order,
            });
            setPatients(data.items);
            setTotal(data.total);
            setTotalPages(data.totalPages);
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Impossible de charger les patients.';
            setError(message);
            showToast({ type: 'error', message });
        } finally {
            setIsLoading(false);
        }
    }, [filters.search, page, limit, sort, order, showToast]);

    useEffect(() => {
        load();
    }, [load]);

    const updateFilters = useCallback((next: PatientsFilters) => {
        setFilters(next);
        setPage(1);
    }, []);

    const setSorting = useCallback((key: string | null, dir: 'asc' | 'desc' = 'asc') => {
        setSort(key);
        setOrder(dir);
        setPage(1);
    }, []);

    return {
        patients,
        total,
        totalPages,
        page,
        limit,
        filters,
        setFilters: updateFilters,
        isLoading,
        error,
        refetch: load,
        setPage,
        setSort: setSorting,
    };
}