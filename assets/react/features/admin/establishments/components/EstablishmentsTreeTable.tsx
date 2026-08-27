import React from 'react';
import { TreeTable } from '@/react/components/Data/TreeTable/TreeTable';
import { Card } from '@/react/components/UI/Card';   // import Card
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { EstablishmentTreeNodeData } from '../hooks/useEstablishments';
import { TreeTableColumn, TreeTableNode } from "@/react/hook-components/Data/TreeTable/types";

interface EstablishmentsTreeTableProps {
    nodes: TreeTableNode<EstablishmentTreeNodeData>[];
    filter?: string; // ✅ nouvelle prop
    onViewDetails?: (node: TreeTableNode<EstablishmentTreeNodeData>) => void;
}

export function EstablishmentsTreeTable({ nodes,filter, onViewDetails }: EstablishmentsTreeTableProps) {
    const columns: TreeTableColumn<EstablishmentTreeNodeData>[] = [
        {
            key: 'nom',
            title: 'Nom',
            sortable: true,
        },
        {
            key: 'adresse',
            title: 'Adresse',
            render: (node) => {
                if (node.data?.type === 'establishment' && node.data.establishment) {
                    return node.data.establishment.adresse;
                }
                return '—';
            },
        },
        {
            key: 'telephone',
            title: 'Téléphone',
            render: (node) => {
                if (node.data?.type === 'establishment' && node.data.establishment) {
                    return node.data.establishment.telephone;
                }
                if (node.data?.type === 'department' && node.data.department) {
                    return `${node.data.department.personnel} pers.`;
                }
                return '—';
            },
        },
        {
            key: 'statut',
            title: 'Statut',
            render: (node) => {
                if (node.data?.type === 'establishment' && node.data.establishment) {
                    const statut = node.data.establishment.statut;
                    return <Badge variant={statut === 'Active' ? 'success' : 'error'}>{statut}</Badge>;
                }
                if (node.data?.type === 'department' && node.data.department) {
                    const statut = node.data.department.statut;
                    return <Badge variant={statut === 'Active' ? 'success' : 'error'}>{statut}</Badge>;
                }
                return null;
            },
        },
        {
            key: 'actions',
            title: 'Actions',
            render: (node) => (
                <Button
                    variant="secondary"
                    size="small"
                    onClick={() => onViewDetails?.(node)}
                >
                    Détails
                </Button>
            ),
        },
    ];

    return (
        <Card className="establishments-card">
            <TreeTable
                nodes={nodes}
                columns={columns}
                treeColumnKey="nom"
                filter={filter}
                onNodeClick={(node) => console.log('Clic simple', node.label)}
                onNodeDoubleClick={(node) => console.log('Double clic', node.label)}
                selectable
                variant="striped"
                hoverable
            />
        </Card>
    );
}
