import React from 'react';
import { Card } from '@/react/components/UI/Card';
import { Tree } from '@/react/components/Data/Tree/Tree';
import { TreeNode } from '@/react/hook-components/Data/Tree/types';
import { Tooltip } from '@/react/components/UI/Tooltip';
import '@/styles/pages/root/organisations/OrganisationsTree.scss';
import {
    AddIcon,
    AdminIcon,
    ModifyIcon,
    SuspendIcon
} from "@/react/features/root/organisations/components/OrganisationIcons";

interface OrganisationsTreeProps {
    treeNodes: TreeNode[];
    filter?: string;
    onAction?: (action: string, node: TreeNode) => void;
}

// ✅ Composant local pour bouton avec tooltip
function ActionButton({
                          label,
                          onClick,
                          children,
                      }: {
    label: string;
    onClick: (e: React.MouseEvent) => void;
    children: React.ReactNode;
}) {
    return (
        <Tooltip content={label}>
            <button
                className="tree-action-btn"
                aria-label={label}
                onClick={onClick}
            >
                {children}
            </button>
        </Tooltip>
    );
}

export function OrganisationsTree({ treeNodes, filter, onAction }: OrganisationsTreeProps) {
    const enhanceNodes = (nodes: TreeNode[], level: number): TreeNode[] => {
        return nodes.map((node) => {
            let actions: React.ReactNode = null;

            if (level === 0) {
                actions = (
                    <>
                        <ActionButton label="Modifier" onClick={(e) => { e.stopPropagation(); onAction?.('modify', node); }}>
                            <ModifyIcon />
                        </ActionButton>
                        <ActionButton label="Ajouter un établissement" onClick={(e) => { e.stopPropagation(); onAction?.('add-establishment', node); }}>
                            <AddIcon />
                        </ActionButton>
                        <ActionButton label="Ajouter un admin" onClick={(e) => { e.stopPropagation(); onAction?.('add-admin', node); }}>
                            <AdminIcon />
                        </ActionButton>
                        <ActionButton label="Suspendre" onClick={(e) => { e.stopPropagation(); onAction?.('suspend', node); }}>
                            <SuspendIcon />
                        </ActionButton>
                    </>
                );
            } else if (level === 1) {
                actions = (
                    <>
                        <ActionButton label="Modifier" onClick={(e) => { e.stopPropagation(); onAction?.('modify', node); }}>
                            <ModifyIcon />
                        </ActionButton>
                        <ActionButton label="Ajouter un département" onClick={(e) => { e.stopPropagation(); onAction?.('add-department', node); }}>
                            <AddIcon />
                        </ActionButton>
                        <ActionButton label="Suspendre" onClick={(e) => { e.stopPropagation(); onAction?.('suspend', node); }}>
                            <SuspendIcon />
                        </ActionButton>
                    </>
                );
            } else if (level === 2) {
                actions = (
                    <>
                        <ActionButton label="Modifier" onClick={(e) => { e.stopPropagation(); onAction?.('modify', node); }}>
                            <ModifyIcon />
                        </ActionButton>
                        <ActionButton label="Suspendre" onClick={(e) => { e.stopPropagation(); onAction?.('suspend', node); }}>
                            <SuspendIcon />
                        </ActionButton>
                    </>
                );
            }

            return {
                ...node,
                actions,
                children: node.children ? enhanceNodes(node.children, level + 1) : undefined,
            };
        });
    };

    const enhancedNodes = enhanceNodes(treeNodes, 0);

    return (
        <Card className="organisations-card">
            <Tree
                nodes={enhancedNodes}
                filter={filter}
                selectable
                onNodeClick={(node: TreeNode) => console.log('Clic simple', node.label)}
                onNodeDoubleClick={(node: TreeNode) => console.log('Double clic', node.label)}
                showLines
                variant="bordered"
            />
        </Card>
    );
}
