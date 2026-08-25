import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Organisation } from '../types';

interface OrganisationsTableProps {
    organisations: Organisation[];
}

export function OrganisationsTable({ organisations }: OrganisationsTableProps) {
    const columns = [
        { key: 'nom', title: 'Nom' },
        { key: 'type', title: 'Type' },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: Organisation) => (
                <Badge variant={row.statut === 'Active' ? 'success' : 'error'}>
                    {row.statut}
                </Badge>
            ),
        },
    ];

    return (
        <Card className="organisations-card">
            <DataTable
                columns={columns}
                data={organisations}
            />
        </Card>
    );
}
