import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { NutritionistPatient } from '../types';

interface PatientsTableProps {
    patients: NutritionistPatient[];
}

export function NutritionistPatientsTable({ patients }: PatientsTableProps) {
    const columns = [
        { key: 'nom', title: 'Patient' },
        { key: 'dernierPlan', title: 'Dernier plan', render: (row: NutritionistPatient) => row.dernierPlan ?? '—' },
        { key: 'prochainRendezVous', title: 'Prochain RDV', render: (row: NutritionistPatient) => row.prochainRendezVous ?? '—' },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: NutritionistPatient) => (
                <Badge variant={row.statut === 'Actif' ? 'success' : 'error'}>{row.statut}</Badge>
            ),
        },
        {
            key: 'actions',
            title: 'Actions',
            render: (row: NutritionistPatient) => (
                <Button variant="secondary" size="small" onClick={() => console.log('Dossier', row.id)}>Dossier</Button>
            ),
        },
    ];

    return (
        <Card className="nutritionist-patients-card">
            <DataTable columns={columns} data={patients} />
        </Card>
    );
}
