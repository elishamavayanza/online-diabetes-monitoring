import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Department } from '../types';

interface DepartmentsTableProps {
    departments: Department[];
}

export function DepartmentsTable({ departments }: DepartmentsTableProps) {
    const columns = [
        { key: 'nom', title: 'Nom' },
        { key: 'etablissement', title: 'Établissement' },
        { key: 'specialite', title: 'Spécialité' },
        { key: 'personnel', title: 'Personnel' },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: Department) => (
                <Badge variant={row.statut === 'Active' ? 'success' : 'error'}>
                    {row.statut}
                </Badge>
            ),
        },
        {
            key: 'actions',
            title: 'Actions',
            render: (row: Department) => (
                <Button variant="secondary" size="small" onClick={() => console.log('Détails', row.id)}>
                    Détails
                </Button>
            ),
        },
    ];

    return (
        <Card className="departments-card">
            <DataTable columns={columns} data={departments} />
        </Card>
    );
}
