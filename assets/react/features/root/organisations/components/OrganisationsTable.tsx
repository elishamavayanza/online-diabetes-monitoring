import React from 'react';
import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { IconButton } from '@/react/components/UI/IconButton';
import { Tooltip } from '@/react/components/UI/Tooltip';
import { TreeNode } from '@/react/hook-components/Data/Tree/types';
import { EyeIcon, ModifyIcon, SuspendIcon, AdminIcon } from './OrganisationIcons';

interface OrganisationsTableProps {
    treeNodes: TreeNode[];
    onDetail?: (node: TreeNode) => void;
    onModify?: (node: TreeNode) => void;
    onSuspend?: (node: TreeNode) => void;
    onAddAdmin?: (node: TreeNode) => void;
}

export function OrganisationsTable({
                                       treeNodes,
                                       onDetail,
                                       onModify,
                                       onSuspend,
                                       onAddAdmin,
                                   }: OrganisationsTableProps) {
    const organisations = treeNodes;

    const columns = [
        {
            key: 'avatar',
            title: '',
            render: (node: TreeNode) => {
                const data = node.data as Record<string, unknown> | undefined;
                const logo = data?.logo as string | undefined;
                const name = node.label || '?';
                const initials = name
                    .split(' ')
                    .map((part) => part[0])
                    .join('')
                    .slice(0, 2)
                    .toUpperCase();

                return logo ? (
                    <img
                        src={logo}
                        alt={name}
                        className="organisations-table__avatar-img"
                    />
                ) : (
                    <div className="organisations-table__avatar-initials">
                        {initials}
                    </div>
                );
            },
        },
        {
            key: 'nom',
            title: 'Nom',
            render: (node: TreeNode) => <span>{node.label}</span>,
        },
        {
            key: 'actions',
            title: 'Actions',
            render: (node: TreeNode) => (
                <div className="organisations-table__actions">
                    <Tooltip content="Détail">
                        <IconButton
                            onClick={() => onDetail?.(node)}
                            aria-label="Voir détails"
                            icon={<EyeIcon />}
                        />
                    </Tooltip>
                    <Tooltip content="Modifier">
                        <IconButton
                            onClick={() => onModify?.(node)}
                            aria-label="Modifier"
                            icon={<ModifyIcon />}
                        />
                    </Tooltip>
                    <Tooltip content="Ajouter un admin">
                        <IconButton
                            onClick={() => onAddAdmin?.(node)}
                            aria-label="Ajouter un admin"
                            icon={<AdminIcon />}
                        />
                    </Tooltip>
                    <Tooltip content="Suspendre">
                        <IconButton
                            onClick={() => onSuspend?.(node)}
                            aria-label="Suspendre"
                            icon={<SuspendIcon />}
                        />
                    </Tooltip>
                </div>
            ),
        },
    ];

    return (
        <Card className="organisations-card">
            <DataTable columns={columns} data={organisations} />
        </Card>
    );
}
