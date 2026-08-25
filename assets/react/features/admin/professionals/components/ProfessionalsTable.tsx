import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Professional } from '../types';

interface ProfessionalsTableProps {
    professionals: Professional[];
}

export function ProfessionalsTable({ professionals }: ProfessionalsTableProps) {
    const columns = [
        { key: 'nom', title: 'Nom' },
        { key: 'type', title: 'Type' },
        { key: 'specialite', title: 'Spécialité' },
        { key: 'etablissement', title: 'Établissement' },
        { key: 'departement', title: 'Département' },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: Professional) => (
                <Badge variant={row.statut === 'Active' ? 'success' : 'error'}>
                    {row.statut}
                </Badge>
            ),
        },
        {
            key: 'actions',
            title: 'Actions',
            render: (row: Professional) => (
                <Button variant="secondary" size="small" onClick={() => console.log('Détails', row.id)}>
                    Détails
                </Button>
            ),
        },
    ];

    return (
        <Card className="professionals-card">
            <DataTable columns={columns} data={professionals} />
        </Card>
    );
}
