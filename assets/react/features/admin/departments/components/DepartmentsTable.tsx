import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Department } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

interface DepartmentsTableProps {
    departments: Department[];
}

export function DepartmentsTable({ departments }: DepartmentsTableProps) {
    const { t } = useI18n();

    const columns = [
        { key: 'nom', title: t('Nom') },
        { key: 'etablissement', title: t('Établissement') },
        { key: 'specialite', title: t('Spécialité') },
        { key: 'personnel', title: t('Personnel') },
        {
            key: 'statut',
            title: t('Statut'),
            render: (row: Department) => (
                <Badge variant={row.statut === 'Active' ? 'success' : 'error'}>
                    {row.statut}
                </Badge>
            ),
        },
        {
            key: 'actions',
            title: t('Actions'),
            render: (row: Department) => (
                <Button variant="secondary" size="small" onClick={() => console.log('Détails', row.id)}>
                    {t('Détails')}
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
