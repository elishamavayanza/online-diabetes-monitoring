import { Card } from '@/react/components/UI/Card';
import { Tree } from '@/react/components/Data/Tree/Tree';
import { TreeNode } from '@/react/hook-components/Data/Tree/types'; // ✅ chemin corrigé

interface OrganisationsTreeProps {
    treeNodes: TreeNode[];
    filter?: string;
}

export function OrganisationsTree({ treeNodes, filter }: OrganisationsTreeProps) {
    return (
        <Card className="organisations-card">
            <Tree
                nodes={treeNodes}
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
