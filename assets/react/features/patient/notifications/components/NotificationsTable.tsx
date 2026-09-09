import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { PatientNotification, PatientNotificationType } from '../types';

interface NotificationsTableProps {
    notifications: PatientNotification[];
    onMarkAsRead?: (id: string) => void;
}

const typeVariant: Record<PatientNotificationType, 'success' | 'warning' | 'error' | 'primary'> = {
    MEDICATION_REMINDER: 'error',
    APPOINTMENT: 'primary',
    NEW_MESSAGE: 'success',
    PRESCRIPTION_UPDATED: 'warning',
    MEASUREMENT_REMINDER: 'primary',
    SYSTEM_ALERT: 'error',
};

export function NotificationsTable({ notifications, onMarkAsRead }: NotificationsTableProps) {
    const columns = [
        { key: 'titre', title: 'Titre' },
        { key: 'message', title: 'Message' },
        {
            key: 'type',
            title: 'Type',
            render: (row: PatientNotification) => (
                <Badge variant={typeVariant[row.type]}>{row.type}</Badge>
            ),
        },
        {
            key: 'estLue',
            title: 'État',
            render: (row: PatientNotification) => (
                <Badge variant={row.estLue ? 'success' : 'warning'}>
                    {row.estLue ? 'Lue' : 'Non lue'}
                </Badge>
            ),
        },
        { key: 'date', title: 'Date' },
        {
            key: 'actions',
            title: 'Actions',
            render: (row: PatientNotification) => (
                <div className="notifications-table__actions">
                    {!row.estLue && onMarkAsRead && (
                        <Button variant="outline" size="small" onClick={() => onMarkAsRead(row.id)}>
                            Marquer lue
                        </Button>
                    )}
                </div>
            ),
        },
    ];

    return (
        <Card className="notifications-card">
            <DataTable columns={columns} data={notifications} />
        </Card>
    );
}
