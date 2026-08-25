import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { AdminNotification, AdminNotificationType } from '../types';

interface NotificationsTableProps {
    notifications: AdminNotification[];
}

const typeVariant: Record<AdminNotificationType, 'success' | 'warning' | 'error'> = {
    SYSTEM_ALERT: 'error',
    MESSAGE_RECEIVED: 'success',
    APPOINTMENT_CREATED: 'warning',
};

export function NotificationsTable({ notifications }: NotificationsTableProps) {
    const columns = [
        { key: 'titre', title: 'Titre' },
        { key: 'message', title: 'Message' },
        {
            key: 'type',
            title: 'Type',
            render: (row: AdminNotification) => (
                <Badge variant={typeVariant[row.type]}>
                    {row.type}
                </Badge>
            ),
        },
        {
            key: 'estLue',
            title: 'État',
            render: (row: AdminNotification) => (
                <Badge variant={row.estLue ? 'success' : 'warning'}>
                    {row.estLue ? 'Lue' : 'Non lue'}
                </Badge>
            ),
        },
        { key: 'date', title: 'Date' },
    ];

    return (
        <Card className="admin-notifications-card">
            <DataTable columns={columns} data={notifications} />
        </Card>
    );
}
