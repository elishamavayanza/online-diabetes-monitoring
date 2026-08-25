import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Appointment } from '../types';

interface AppointmentsTableProps {
    appointments: Appointment[];
}

const statusVariant: Record<string, 'success' | 'warning' | 'error' | 'primary'> = {
    'Confirmé': 'success',
    'En attente': 'warning',
    'Terminé': 'primary',
    'Annulé': 'error',
};

export function AppointmentsTable({ appointments }: AppointmentsTableProps) {
    const columns = [
        { key: 'patient', title: 'Patient' },
        { key: 'date', title: 'Date' },
        { key: 'heure', title: 'Heure' },
        { key: 'motif', title: 'Motif' },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: Appointment) => (
                <Badge variant={statusVariant[row.statut]}>{row.statut}</Badge>
            ),
        },
    ];

    return (
        <Card className="appointments-card">
            <DataTable columns={columns} data={appointments} />
        </Card>
    );
}
