import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { NutritionistNotification, NotificationType } from '../types';

interface NotificationsTableProps {
    notifications: NutritionistNotification[];
}

const typeVariant: Record<NotificationType, 'success' | 'warning' | 'error' | 'primary'> = {
    NEW_APPOINTMENT: 'primary',
    PLAN_UPDATED: 'warning',
    NEW_MESSAGE: 'success',
    APPOINTMENT_SOON: 'error',
    PATIENT_ADDED: 'primary',
};

export function NotificationsTable({ notifications }: NotificationsTableProps) {
    const columns = [
        { key: 'titre', title: 'Titre' },
        { key: 'message', title: 'Message' },
        {
            key: 'type',
            title: 'Type',
            render: (row: NutritionistNotification) => (
                <Badge variant={typeVariant[row.type]}>{row.type}</Badge>
            ),
        },
        {
            key: 'estLue',
            title: 'État',
            render: (row: NutritionistNotification) => (
                <Badge variant={row.estLue ? 'success' : 'warning'}>{row.estLue ? 'Lue' : 'Non lue'}</Badge>
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
