import { useEffect, useState } from 'react';
import { fetchEstablishments } from '../services/establishmentsService';
import { fetchDepartments } from '../../departments/services/departmentsService';  // ✅ chemin corrigé
import {Establishment} from "@/react/features/admin/establishments/types";
import {Department} from "@/react/features/admin/departments/types";
import {TreeTableNode} from "@/react/hook-components/Data/TreeTable/types";             // ✅ chemin composant

export interface EstablishmentTreeNodeData {
    type: 'establishment' | 'department';
    establishment?: Establishment;
    department?: Department;
}

export function useEstablishments() {
    const [treeNodes, setTreeNodes] = useState<TreeTableNode<EstablishmentTreeNodeData>[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const [estRes, deptRes] = await Promise.all([
                    fetchEstablishments(),
                    fetchDepartments(),
                ]);

                const establishments: Establishment[] = estRes.establishments;
                const departments: Department[] = deptRes;

                const nodes: TreeTableNode<EstablishmentTreeNodeData>[] = establishments.map((est: Establishment) => {
                    const children: TreeTableNode<EstablishmentTreeNodeData>[] = departments
                        .filter((dep: Department) => dep.etablissement === est.nom)
                        .map((dep: Department) => ({
                            id: `dept-${dep.id}`,
                            label: dep.nom,
                            data: { type: 'department', department: dep },
                            icon: undefined,
                        }));

                    return {
                        id: `est-${est.id}`,
                        label: est.nom,
                        data: { type: 'establishment', establishment: est },
                        icon: undefined,
                        children: children.length > 0 ? children : undefined,
                    };
                });

                setTreeNodes(nodes);
            } catch (err) {
                setError('Impossible de charger les établissements.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { treeNodes, isLoading, error };
}
