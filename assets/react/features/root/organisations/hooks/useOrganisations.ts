// hooks/useOrganisations.ts
import { useEffect, useState } from 'react';
import { fetchOrganisations } from '../services/organisationsService';
import {TreeNode} from "@/react/hook-components/Data/Tree/types";

export function useOrganisations() {
    const [treeNodes, setTreeNodes] = useState<TreeNode[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const data = await fetchOrganisations();
                setTreeNodes(data);
            } catch (err) {
                setError('Impossible de charger les organisations.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { treeNodes, isLoading, error };
}
