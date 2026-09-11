import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { useI18n } from '@/react/i18n/I18nContext';
import { MealPlan } from '../types';

interface MealPlansTableProps {
    plans: MealPlan[];
}

export function MealPlansTable({ plans }: MealPlansTableProps) {
    const { t } = useI18n();
    const columns = [
        { key: 'patient', title: t('Patient') },
        { key: 'titre', title: t('Titre') },
        { key: 'dateCreation', title: t('Date de création') },
        {
            key: 'statut',
            title: t('Statut'),
            render: (row: MealPlan) => (
                <Badge variant={row.statut === 'Actif' ? 'success' : 'warning'}>{t(row.statut)}</Badge>
            ),
        },
        {
            key: 'actions',
            title: t('Actions'),
            render: (row: MealPlan) => (
                <Button variant="secondary" size="small" onClick={() => console.log('Détails', row.id)}>{t('Détails')}</Button>
            ),
        },
    ];

    return (
        <Card className="meal-plans-card">
            <DataTable columns={columns} data={plans} />
        </Card>
    );
}
