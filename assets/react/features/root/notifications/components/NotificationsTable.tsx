import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Notification, NotificationType } from '../types';

interface NotificationsTableProps {
    notifications: Notification[];
}

const typeVariant: Record<NotificationType, 'success' | 'warning' | 'error'> = {
    SYSTEM_ALERT: 'error',
    MESSAGE_RECEIVED: 'success',
    PRESCRIPTION_UPDATED: 'warning',
};

export function NotificationsTable({ notifications }: NotificationsTableProps) {
    const columns = [
        { key: 'titre', title: 'Titre' },
        { key: 'message', title: 'Message' },
        {
            key: 'type',
            title: 'Type',
            render: (row: Notification) => (
                <Badge variant={typeVariant[row.type]}>
                    {row.type}
                </Badge>
            ),
        },
        {
            key: 'estLue',
            title: 'État',
            render: (row: Notification) => (
                <Badge variant={row.estLue ? 'success' : 'warning'}>
                    {row.estLue ? 'Lue' : 'Non lue'}
                </Badge>
            ),
        },
        { key: 'date', title: 'Date' },
    ];

    return (
        <Card className="notifications-card">
            <DataTable columns={columns} data={notifications} />
        </Card>
    );
}
