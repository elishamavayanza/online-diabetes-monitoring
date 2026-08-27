// features/admin/establishments/hooks/useEstablishmentDetail.ts
import { useEffect, useState } from 'react';
import { useEstablishments, EstablishmentTreeNodeData } from './useEstablishments';
import { TreeTableNode } from '@/react/hook-components/Data/TreeTable/types';

// Fonction récursive pour trouver un nœud par son ID
function findNodeById(nodes: TreeTableNode<EstablishmentTreeNodeData>[], id: string): TreeTableNode<EstablishmentTreeNodeData> | null {
    for (const node of nodes) {
        if (node.id === id) return node;
        if (node.children) {
            const found = findNodeById(node.children, id);
            if (found) return found;
        }
    }
    return null;
}

export function useEstablishmentDetail(id: string) {
    const { treeNodes, isLoading, error } = useEstablishments();
    const [node, setNode] = useState<TreeTableNode<EstablishmentTreeNodeData> | null>(null);

    useEffect(() => {
        if (treeNodes.length > 0) {
            setNode(findNodeById(treeNodes, id));
        }
    }, [treeNodes, id]);

    return { node, isLoading, error };
}
