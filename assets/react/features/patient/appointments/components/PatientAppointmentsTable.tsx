import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { PatientAppointment } from '../types';

interface PatientAppointmentsTableProps {
    appointments: PatientAppointment[];
}

const statusVariant: Record<string, 'success' | 'warning' | 'error' | 'primary'> = {
    'Confirmé': 'success',
    'En attente': 'warning',
    'Terminé': 'primary',
    'Annulé': 'error',
};

export function PatientAppointmentsTable({ appointments }: PatientAppointmentsTableProps) {
    const columns = [
        { key: 'date', title: 'Date' },
        { key: 'heure', title: 'Heure' },
        { key: 'professionnel', title: 'Professionnel' },
        { key: 'motif', title: 'Motif' },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: PatientAppointment) => (
                <Badge variant={statusVariant[row.statut]}>{row.statut}</Badge>
            ),
        },
    ];

    return (
        <Card className="patient-appointments-card">
            <DataTable columns={columns} data={appointments} />
        </Card>
    );
}
