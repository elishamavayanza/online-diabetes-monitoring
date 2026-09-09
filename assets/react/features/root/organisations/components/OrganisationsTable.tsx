import React from 'react';
import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { IconButton } from '@/react/components/UI/IconButton';
import { Button } from '@/react/components/UI/Button';
import { Tooltip } from '@/react/components/UI/Tooltip';
import { TreeNode } from '@/react/hook-components/Data/Tree/types';
import { EyeIcon, ModifyIcon, SuspendIcon, AdminIcon } from './OrganisationIcons';
import { useI18n } from '@/react/i18n/I18nContext';

interface OrganisationsTableProps {
    treeNodes: TreeNode[];
    onDetail?: (node: TreeNode) => void;
    onModify?: (node: TreeNode) => void;
    onSuspend?: (node: TreeNode) => void;
    onReactivate?: (node: TreeNode) => void;
    onAddAdmin?: (node: TreeNode) => void;
}

export function OrganisationsTable({
                                       treeNodes,
                                       onDetail,
                                       onModify,
                                       onSuspend,
                                       onReactivate,
                                       onAddAdmin,
                                   }: OrganisationsTableProps) {
    const { t } = useI18n();
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
            title: t('Nom'),
            render: (node: TreeNode) => <span>{node.label}</span>,
        },
        {
            key: 'actions',
            title: t('Actions'),
            render: (node: TreeNode) => {
                const data = node.data as Record<string, unknown> | undefined;
                return (
                <div className="organisations-table__actions">
                    <Tooltip content={t('Détail')}>
                        <IconButton
                            onClick={() => onDetail?.(node)}
                            aria-label={t('Voir détails')}
                            icon={<EyeIcon />}
                        />
                    </Tooltip>
                    <Tooltip content={t('Modifier')}>
                        <IconButton
                            onClick={() => onModify?.(node)}
                            aria-label={t('Modifier')}
                            icon={<ModifyIcon />}
                        />
                    </Tooltip>
                    <Tooltip content={t('Ajouter un admin')}>
                        <IconButton
                            onClick={() => onAddAdmin?.(node)}
                            aria-label={t('Ajouter un admin')}
                            icon={<AdminIcon />}
                        />
                    </Tooltip>
                    <Tooltip content={t('Suspendre')}>
                        <IconButton
                            onClick={() => onSuspend?.(node)}
                            aria-label={t('Suspendre')}
                            icon={<SuspendIcon />}
                        />
                    </Tooltip>
                    {data?.dataType === 'organisation' && data?.active === false && (
                        <Tooltip content={t('Réactiver')}>
                            <Button
                                variant="success"
                                size="small"
                                onClick={() => onReactivate?.(node)}
                            >
                                {t('Réactiver')}
                            </Button>
                        </Tooltip>
                    )}
                </div>
                );
            },
        },
    ];

    return (
        <Card className="organisations-card">
            <DataTable columns={columns} data={organisations} />
        </Card>
    );
}
