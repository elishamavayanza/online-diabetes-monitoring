import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { MealPlan } from '../types';

interface MealPlansTableProps {
    plans: MealPlan[];
}

export function MealPlansTable({ plans }: MealPlansTableProps) {
    const columns = [
        { key: 'patient', title: 'Patient' },
        { key: 'titre', title: 'Titre' },
        { key: 'dateCreation', title: 'Date de création' },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: MealPlan) => (
                <Badge variant={row.statut === 'Actif' ? 'success' : 'warning'}>{row.statut}</Badge>
            ),
        },
        {
            key: 'actions',
            title: 'Actions',
            render: (row: MealPlan) => (
                <Button variant="secondary" size="small" onClick={() => console.log('Détails', row.id)}>Détails</Button>
            ),
        },
    ];

    return (
        <Card className="meal-plans-card">
            <DataTable columns={columns} data={plans} />
        </Card>
    );
}
