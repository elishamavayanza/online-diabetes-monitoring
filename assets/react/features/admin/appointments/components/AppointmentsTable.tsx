import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Appointment } from '../types';

interface AppointmentsTableProps {
    appointments: Appointment[];
}

const statusVariant: Record<string, 'success' | 'warning' | 'error'> = {
    Confirmed: 'success',
    Pending: 'warning',
    Cancelled: 'error',
};

export function AppointmentsTable({ appointments }: AppointmentsTableProps) {
    const columns = [
        { key: 'patient', title: 'Patient' },
        { key: 'professionnel', title: 'Professionnel' },
        { key: 'etablissement', title: 'Établissement' },
        { key: 'date', title: 'Date' },
        { key: 'heure', title: 'Heure' },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: Appointment) => (
                <Badge variant={statusVariant[row.statut]}>
                    {row.statut}
                </Badge>
            ),
        },
    ];

    return (
        <Card className="appointments-card">
            <DataTable columns={columns} data={appointments} />
        </Card>
    );
}
