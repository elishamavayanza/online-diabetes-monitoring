import { useI18n } from '@/react/i18n/I18nContext';
import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { AdminNotification, AdminNotificationType } from '../types';

interface NotificationsTableProps {
    notifications: AdminNotification[];
    onMarkAsRead?: (id: string) => void;
}

const typeVariant: Record<AdminNotificationType, 'success' | 'warning' | 'error'> = {
    SYSTEM_ALERT: 'error',
    MESSAGE_RECEIVED: 'success',
    APPOINTMENT_CREATED: 'warning',
};

export function NotificationsTable({ notifications, onMarkAsRead }: NotificationsTableProps) {
    const { t } = useI18n();

    const columns = [
        { key: 'titre', title: t('Titre') },
        { key: 'message', title: t('Message') },
        {
            key: 'type',
            title: t('Type'),
            render: (row: AdminNotification) => (
                <Badge variant={typeVariant[row.type]}>
                    {row.type}
                </Badge>
            ),
        },
        {
            key: 'estLue',
            title: t('État'),
            render: (row: AdminNotification) => (
                <Badge variant={row.estLue ? 'success' : 'warning'}>
                    {row.estLue ? t('Lue') : t('Non lue')}
                </Badge>
            ),
        },
        { key: 'date', title: t('Date') },
        {
            key: 'actions',
            title: t('Actions'),
            render: (row: AdminNotification) => (
                <div className="admin-notifications-table__actions">
                    {!row.estLue && onMarkAsRead && (
                        <Button variant="outline" size="small" onClick={() => onMarkAsRead(row.id)}>
                            {t('Marquer lue')}
                        </Button>
                    )}
                </div>
            ),
        },
    ];

    return (
        <Card className="admin-notifications-card">
            <DataTable columns={columns} data={notifications} />
        </Card>
    );
}
