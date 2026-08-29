// hooks/useOrganisations.ts
import { useEffect, useState, useCallback } from 'react';
import { fetchOrganisations } from '../services/organisationsService';
import { TreeNode } from "@/react/hook-components/Data/Tree/types";

export function useOrganisations() {
    const [treeNodes, setTreeNodes] = useState<TreeNode[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchOrganisations();
            setTreeNodes(data);
        } catch (err) {
            console.error('Erreur chargement organisations:', err);
            setError(err instanceof Error ? err.message : 'Impossible de charger les organisations.');
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        load();
    }, [load]);

    return { treeNodes, isLoading, error, refetch: load };
}
