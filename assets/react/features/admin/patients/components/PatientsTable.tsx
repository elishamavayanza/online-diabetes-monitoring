import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Patient } from '../types';

interface PatientsTableProps {
    patients: Patient[];
}

export function PatientsTable({ patients }: PatientsTableProps) {
    const columns = [
        { key: 'nom', title: 'Nom' },
        { key: 'dateNaissance', title: 'Date de naissance' },
        { key: 'typeDiabete', title: 'Type de diabète' },
        { key: 'equipeSoins', title: 'Équipe de soins' },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: Patient) => (
                <Badge variant={row.statut === 'Active' ? 'success' : 'error'}>
                    {row.statut}
                </Badge>
            ),
        },
        {
            key: 'actions',
            title: 'Actions',
            render: (row: Patient) => (
                <Button variant="secondary" size="small" onClick={() => console.log('Détails', row.id)}>
                    Détails
                </Button>
            ),
        },
    ];

    return (
        <Card className="patients-card">
            <DataTable columns={columns} data={patients} />
        </Card>
    );
}
