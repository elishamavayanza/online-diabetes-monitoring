import { useEffect, useState } from 'react';
import { fetchEstablishments } from '../services/establishmentsService';
import { fetchDepartments } from '../../departments/services/departmentsService';
import { TreeTableNode } from '@/react/hook-components/Data/TreeTable/types';
import { Establishment } from '@/react/features/admin/establishments/types';
import { Department } from '@/react/features/admin/departments/types';

export interface EstablishmentTreeNodeData {
    type: 'establishment' | 'department';
    establishment?: Establishment & {
        personnelCount?: number;
        patientCount?: number;
    };
    department?: Department & {
        patients?: number;
    };
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

                const establishments = estRes.establishments;
                const departments = deptRes; // Department[]

                const nodes: TreeTableNode<EstablishmentTreeNodeData>[] = establishments.map((est: Establishment) => {
                    const estChildren = departments.filter((dep: Department) => dep.etablissement === est.nom);

                    return {
                        id: `est-${est.id}`,
                        label: est.nom,
                        data: {
                            type: 'establishment',
                            establishment: {
                                ...est,
                                personnelCount: estChildren.reduce((sum, dep) => sum + dep.personnel, 0),
                                patientCount: estChildren.length * 15, // exemple
                            },
                        },
                        icon: undefined,
                        children: estChildren.map((dep: Department) => ({
                            id: `dept-${dep.id}`,
                            label: dep.nom,
                            data: {
                                type: 'department',
                                department: {
                                    ...dep,
                                    patients: dep.personnel * 2, // exemple
                                },
                            },
                            icon: undefined,
                        })),
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
