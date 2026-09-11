import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { DataAccessLog } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

interface DataAccessLogsTableProps {
    logs: DataAccessLog[];
}

export function DataAccessLogsTable({ logs }: DataAccessLogsTableProps) {
    const { t } = useI18n();
    const columns = [
        { key: 'utilisateur', title: t('Utilisateur') },
        { key: 'patient', title: t('Patient') },
        { key: 'ressourceConsultee', title: t('Ressource consultée') },
        { key: 'motif', title: t('Motif') },
        { key: 'date', title: t('Date') },
    ];

    return (
        <Card className="audit-card">
            <h2>{t('Accès aux données médicales')}</h2>
            <DataTable columns={columns} data={logs} />
        </Card>
    );
}
