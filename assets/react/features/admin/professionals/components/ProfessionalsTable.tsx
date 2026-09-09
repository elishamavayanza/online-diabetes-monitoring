import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Professional } from '../types/types';
import { useI18n } from '@/react/i18n/I18nContext';

interface ProfessionalsTableProps {
    professionals: Professional[];
    total: number;
    page: number;
    limit: number;
    loading: boolean;
    onPageChange: (page: number) => void;
    onSort: (key: string, direction: 'asc' | 'desc') => void;
    onViewDetails?: (professional: Professional) => void;
    onSuspend?: (professional: Professional) => void;
    onReactivate?: (professional: Professional) => void;
}

export function ProfessionalsTable({
                                        professionals,
                                        total,
                                        page,
                                        limit,
                                        loading,
                                        onPageChange,
                                        onSort,
                                        onViewDetails,
                                        onSuspend,
                                        onReactivate,
                                    }: ProfessionalsTableProps) {
    const { t } = useI18n();
    const columns = [
        { key: 'nom', title: t('Nom'), sortable: true },
        { key: 'type', title: t('Type') },
        {
            key: 'statut',
            title: t('Statut'),
            render: (row: Professional) => (
                <Badge variant={row.statut === 'Active' ? 'success' : row.statut === 'Suspended' ? 'warning' : 'error'}>
                    {row.statut === 'Suspended' ? t('Suspendu') : row.statut}
                </Badge>
            ),
        },
        {
            key: 'actions',
            title: t('Actions'),
            render: (row: Professional) => (
                <div className="professionals-table__actions">
                    <Button
                        variant="secondary"
                        size="small"
                        onClick={() => onViewDetails?.(row)}
                    >
                        {t('Détails')}
                    </Button>
                    {row.statut === 'Suspended' ? (
                        <Button
                            variant="success"
                            size="small"
                            onClick={() => onReactivate?.(row)}
                        >
                            {t('Réactiver')}
                        </Button>
                    ) : (
                        <Button
                            variant="danger"
                            size="small"
                            onClick={() => onSuspend?.(row)}
                        >
                            {t('Suspendre')}
                        </Button>
                    )}
                </div>
            ),
        },
    ];

    return (
        <Card className="professionals-card">
            <DataTable
                columns={columns}
                data={professionals}
                mode="server"
                loading={loading}
                pageSize={limit}
                totalItems={total}
                currentPage={page}
                onPageChange={onPageChange}
                onSort={onSort}
            />
        </Card>
    );
}