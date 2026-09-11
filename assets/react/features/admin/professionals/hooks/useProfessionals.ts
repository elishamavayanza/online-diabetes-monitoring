import { useEffect, useState, useCallback } from 'react';
import { fetchPaginatedProfessionals } from '../services/professionalsService';
import { Professional } from '../types/types';

export function useProfessionals() {
    const [professionals, setProfessionals] = useState<Professional[]>([]);
    const [total, setTotal] = useState(0);
    const [totalPages, setTotalPages] = useState(1);
    const [page, setPage] = useState(1);
    const [limit] = useState(10);
    const [q, setQ] = useState('');
    const [sort, setSort] = useState<string | null>(null);
    const [order, setOrder] = useState<'asc' | 'desc'>('desc');
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchPaginatedProfessionals({
                q,
                page,
                limit,
                sort: sort ?? undefined,
                order,
            });
            setProfessionals(data.items);
            setTotal(data.total);
            setTotalPages(data.totalPages);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Impossible de charger les professionnels.');
        } finally {
            setIsLoading(false);
        }
    }, [q, page, limit, sort, order]);

    useEffect(() => {
        load();
    }, [load]);

    const setSearch = useCallback((next: string) => {
        setQ(next);
        setPage(1);
    }, []);

    const setSorting = useCallback((key: string | null, dir: 'asc' | 'desc' = 'asc') => {
        setSort(key);
        setOrder(dir);
        setPage(1);
    }, []);

    return {
        professionals,
        total,
        totalPages,
        page,
        limit,
        isLoading,
        error,
        refetch: load,
        setSearch,
        setPage,
        setSort: setSorting,
    };
}