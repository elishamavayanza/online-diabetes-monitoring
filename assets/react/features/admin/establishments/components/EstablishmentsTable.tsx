import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Establishment } from '../types';

interface EstablishmentsTableProps {
    establishments: Establishment[];
}

export function EstablishmentsTable({ establishments }: EstablishmentsTableProps) {
    const columns = [
        { key: 'nom', title: 'Nom' },
        { key: 'adresse', title: 'Adresse' },
        { key: 'telephone', title: 'Téléphone' },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: Establishment) => (
                <Badge variant={row.statut === 'Active' ? 'success' : 'error'}>
                    {row.statut}
                </Badge>
            ),
        },
        { key: 'departementsCount', title: 'Départements' },
        {
            key: 'actions',
            title: 'Actions',
            render: (row: Establishment) => (
                <Button variant="secondary" size="small" onClick={() => console.log('Détails', row.id)}>
                    Détails
                </Button>
            ),
        },
    ];

    return (
        <Card className="establishments-card">
            <DataTable columns={columns} data={establishments} />
        </Card>
    );
}
