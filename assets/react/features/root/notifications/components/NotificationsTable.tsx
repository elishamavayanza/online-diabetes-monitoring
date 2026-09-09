import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { Notification, NotificationType } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

interface NotificationsTableProps {
    notifications: Notification[];
    onNotificationClick: (notification: Notification) => void;
    onMarkAsRead?: (id: string) => void;
}

const typeVariant: Partial<Record<NotificationType, 'success' | 'warning' | 'error'>> = {
    SYSTEM_ALERT: 'error',
    MESSAGE_RECEIVED: 'success',
    PRESCRIPTION_UPDATED: 'warning',
};

export function NotificationsTable({ notifications, onNotificationClick, onMarkAsRead }: NotificationsTableProps) {
    const { t } = useI18n();
    const columns = [
        { key: 'titre', title: t('Titre') },
        { key: 'message', title: t('Message') },
        {
            key: 'type',
            title: t('Type'),
            render: (row: Notification) => (
                <Badge variant={typeVariant[row.type] ?? 'secondary'}>
                    {row.type}
                </Badge>
            ),
        },
        {
            key: 'estLue',
            title: t('État'),
            render: (row: Notification) => (
                <Badge variant={row.estLue ? 'success' : 'warning'}>
                    {row.estLue ? t('Lue') : t('Non lue')}
                </Badge>
            ),
        },
        { key: 'date', title: t('Date') },
        {
            key: 'actions',
            title: t('Actions'),
            render: (row: Notification) => (
                <div className="notifications-table__actions">
                    {!row.estLue && onMarkAsRead && (
                        <Button variant="outline" size="small" onClick={() => onMarkAsRead(row.id)}>
                            {t('Marquer lue')}
                        </Button>
                    )}
                    <Button variant="secondary" size="small" onClick={() => onNotificationClick(row)}>
                        {t('Détails')}
                    </Button>
                </div>
            ),
        },
    ];

    return (
        <Card className="notifications-card">
            <DataTable
                columns={columns}
                data={notifications}
                pageSize={10}
            />
        </Card>
    );
}
