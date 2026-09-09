import { useCallback, useEffect, useState } from 'react';
import { fetchPaginatedUsers, UsersQuery } from '../services/usersService';
import { User } from '../types';

export type UserFilterTab = 'Tous' | 'Professionnels' | 'Patients' | 'Administrateurs';

interface UseUsersResult {
    users: User[];
    total: number;
    totalPages: number;
    page: number;
    limit: number;
    isLoading: boolean;
    error: string | null;
    filter: UserFilterTab;
    q: string;
    sort: string | null;
    order: 'asc' | 'desc';
    setFilter: (filter: UserFilterTab) => void;
    setQ: (q: string) => void;
    setPage: (page: number) => void;
    setSort: (sort: string | null, order?: 'asc' | 'desc') => void;
    setOrg: (org?: string) => void;
}

export function useUsers(initialFilter: UserFilterTab = 'Tous'): UseUsersResult {
    const [filter, setFilter] = useState<UserFilterTab>(initialFilter);
    const [q, setQ] = useState('');
    const [org, setOrgId] = useState<string | undefined>(undefined);
    const [page, setPage] = useState(1);
    const [limit] = useState(10);
    const [sort, setSortKey] = useState<string | null>(null);
    const [order, setOrder] = useState<'asc' | 'desc'>('desc');

    const [users, setUsers] = useState<User[]>([]);
    const [total, setTotal] = useState(0);
    const [totalPages, setTotalPages] = useState(1);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        let cancelled = false;

        const load = async () => {
            setIsLoading(true);
            setError(null);

            const query: UsersQuery = {
                tab: filter,
                q,
                page,
                limit,
                sort: sort ?? undefined,
                order,
                org,
            };

            try {
                const data = await fetchPaginatedUsers(query);
                if (cancelled) return;
                setUsers(data.items);
                setTotal(data.total);
                setTotalPages(data.totalPages);
            } catch (err) {
                if (cancelled) return;
                setError('Impossible de charger les utilisateurs.');
            } finally {
                if (!cancelled) setIsLoading(false);
            }
        };

        load();

        return () => {
            cancelled = true;
        };
    }, [filter, q, page, limit, sort, order, org]);

    const handleFilterChange = useCallback((next: UserFilterTab) => {
        setFilter(next);
        setPage(1);
    }, []);

    const handleQChange = useCallback((next: string) => {
        setQ(next);
        setPage(1);
    }, []);

    const handleSortChange = useCallback((key: string | null, dir: 'asc' | 'desc' = 'asc') => {
        setSortKey(key);
        setOrder(dir);
        setPage(1);
    }, []);

    const handleOrgChange = useCallback((next?: string) => {
        setOrgId(next);
        setPage(1);
    }, []);

    return {
        users,
        total,
        totalPages,
        page,
        limit,
        isLoading,
        error,
        filter,
        q,
        sort,
        order,
        setFilter: handleFilterChange,
        setQ: handleQChange,
        setPage,
        setSort: handleSortChange,
        setOrg: handleOrgChange,
    };
}