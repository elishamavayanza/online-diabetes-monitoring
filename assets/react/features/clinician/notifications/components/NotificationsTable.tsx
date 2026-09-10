import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { ClinicianNotification, ClinicianNotificationType } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

interface NotificationsTableProps {
    notifications: ClinicianNotification[];
}

const typeVariant: Record<ClinicianNotificationType, 'success' | 'warning' | 'error' | 'primary'> = {
    PRESCRIPTION_UPDATED: 'warning',
    NEW_APPOINTMENT: 'primary',
    APPOINTMENT_IN_30_MIN: 'error',
    NEW_MESSAGE: 'success',
    PATIENT_ADDED_TO_TEAM: 'primary',
};

export function NotificationsTable({ notifications }: NotificationsTableProps) {
    const { t } = useI18n();

    const columns = [
        { key: 'titre', title: t('Titre') },
        { key: 'message', title: t('Message') },
        {
            key: 'type',
            title: t('Type'),
            render: (row: ClinicianNotification) => (
                <Badge variant={typeVariant[row.type]}>
                    {row.type}
                </Badge>
            ),
        },
        {
            key: 'estLue',
            title: t('État'),
            render: (row: ClinicianNotification) => (
                <Badge variant={row.estLue ? 'success' : 'warning'}>
                    {row.estLue ? t('Lue') : t('Non lue')}
                </Badge>
            ),
        },
        { key: 'date', title: t('Date') },
    ];

    return (
        <Card className="clinician-notifications-card">
            <DataTable columns={columns} data={notifications} />
        </Card>
    );
}
