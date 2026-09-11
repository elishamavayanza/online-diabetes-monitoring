import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { AuditLog } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

interface AuditLogsTableProps {
    logs: AuditLog[];
}

export function AuditLogsTable({ logs }: AuditLogsTableProps) {
    const { t } = useI18n();
    const columns = [
        { key: 'utilisateur', title: t('Utilisateur') },
        { key: 'action', title: t('Action') },
        { key: 'ressource', title: t('Ressource') },
        { key: 'date', title: t('Date') },
        { key: 'adresseIp', title: t('Adresse IP') },
        {
            key: 'resultat',
            title: t('Résultat'),
            render: (row: AuditLog) => (
                <Badge variant={row.resultat === 'SUCCESS' ? 'success' : 'error'}>
                    {row.resultat}
                </Badge>
            ),
        },
    ];

    return (
        <Card className="audit-card">
            <h2>{t('Journaux d\u2019audit')}</h2>
            <DataTable columns={columns} data={logs} />
        </Card>
    );
}
