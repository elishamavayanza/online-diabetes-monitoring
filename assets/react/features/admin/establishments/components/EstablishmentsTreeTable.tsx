import React from 'react';
import { TreeTable } from '@/react/components/Data/TreeTable/TreeTable';
import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Tooltip } from '@/react/components/UI/Tooltip';
import { EstablishmentTreeNodeData } from '../hooks/useEstablishments';
import { TreeTableColumn, TreeTableNode } from "@/react/hook-components/Data/TreeTable/types";
import { useI18n } from '@/react/i18n/I18nContext';

const AddIcon = () => (
    <span className="add-icon">
        <svg
            viewBox="0 0 24 24"
            width="16"
            height="16"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
        >
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
    </span>
);

interface EstablishmentsTreeTableProps {
    nodes: TreeTableNode<EstablishmentTreeNodeData>[];
    filter?: string;
    onViewDetails?: (node: TreeTableNode<EstablishmentTreeNodeData>) => void;
    onAddDepartment?: (node: TreeTableNode<EstablishmentTreeNodeData>) => void;
    onNodeDoubleClick?: (node: TreeTableNode<EstablishmentTreeNodeData>) => void;
}

export function EstablishmentsTreeTable({
                                            nodes,
                                            filter,
                                            onViewDetails,
                                            onAddDepartment,
                                            onNodeDoubleClick,
                                        }: EstablishmentsTreeTableProps) {
    const { t } = useI18n();

    const columns: TreeTableColumn<EstablishmentTreeNodeData>[] = [
        {
            key: 'nom',
            title: t('Nom'),
            sortable: true,
        },
        {
            key: 'adresse',
            title: t('Adresse'),
            render: (node) => {
                if (node.data?.type === 'establishment' && node.data.establishment) {
                    return node.data.establishment.adresse;
                }
                return '—';
            },
        },
        {
            key: 'telephone',
            title: t('Téléphone'),
            render: (node) => {
                if (node.data?.type === 'establishment' && node.data.establishment) {
                    return node.data.establishment.telephone;
                }
                if (node.data?.type === 'department' && node.data.department) {
                    return t('{{ count }} pers.', { count: node.data.department.personnel });
                }
                return '—';
            },
        },
        {
            key: 'statut',
            title: t('Statut'),
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
            title: t('Actions'),
            render: (node) => (
                <div style={{ display: 'flex', gap: '0.25rem', alignItems: 'center' }}>
                    {node.data?.type === 'establishment' && (
                        <Tooltip content={t('Ajouter un département')} position="top">
                            <button
                                className="tree-action-btn"
                                onClick={(e) => {
                                    e.stopPropagation();
                                    onAddDepartment?.(node);
                                }}
                            >
                                <AddIcon />
                            </button>
                        </Tooltip>
                    )}
                    <Button
                        variant="secondary"
                        size="small"
                        onClick={() => onViewDetails?.(node)}
                    >
                        {t('Détails')}
                    </Button>
                </div>
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
                onNodeDoubleClick={onNodeDoubleClick} // maintenant disponible
                selectable
                variant="striped"
                hoverable
            />
        </Card>
    );
}
