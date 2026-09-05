import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { PatientAppointment } from '../types';

interface PatientAppointmentsTableProps {
    appointments: PatientAppointment[];
    onCancel?: (appointment: PatientAppointment) => void;
}

const statusVariant: Record<string, 'success' | 'warning' | 'error' | 'primary'> = {
    'Confirmé': 'success',
    'En attente': 'warning',
    'Terminé': 'primary',
    'Annulé': 'error',
    'Absent': 'error',
    'Report demandé': 'warning',
};

export function PatientAppointmentsTable({ appointments, onCancel }: PatientAppointmentsTableProps) {
    const baseColumns: Array<{
        key: string;
        title: string;
        render?: (row: PatientAppointment) => React.ReactElement;
    }> = [
        { key: 'date', title: 'Date' },
        { key: 'heure', title: 'Heure' },
        { key: 'professionnel', title: 'Professionnel' },
        { key: 'motif', title: 'Motif' },
        // Nouvelle colonne Notes
        {
            key: 'notes',
            title: 'Notes',
            render: (row: PatientAppointment) => (
                <span>{row.notes || '—'}</span>
            ),
        },
        {
            key: 'statut',
            title: 'Statut',
            render: (row: PatientAppointment) => (
                <Badge variant={statusVariant[row.statut]}>{row.statut}</Badge>
            ),
        },
    ];

    // Ajouter colonne actions si onCancel est fourni
    if (onCancel) {
        baseColumns.push({
            key: 'actions',
            title: 'Actions',
            render: (row: PatientAppointment) =>
                row.statut === 'Confirmé' ? (
                    <Button
                        variant="danger"
                        size="small"
                        onClick={() => onCancel(row)}
                    >
                        Annuler
                    </Button>
                ) : (
                    <></>
                ),
        });
    }

    return (
        <Card className="patient-appointments-card">
            <DataTable columns={baseColumns} data={appointments} />
        </Card>
    );
}
