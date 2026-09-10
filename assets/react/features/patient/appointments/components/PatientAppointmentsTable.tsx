import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { useI18n } from '@/react/i18n/I18nContext';
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
    const { t } = useI18n();

    const baseColumns: Array<{
        key: string;
        title: string;
        render?: (row: PatientAppointment) => React.ReactElement;
    }> = [
        { key: 'date', title: t('Date') },
        { key: 'heure', title: t('Heure') },
        { key: 'professionnel', title: t('Professionnel') },
        { key: 'motif', title: t('Motif') },
        // Nouvelle colonne Notes
        {
            key: 'notes',
            title: t('Notes'),
            render: (row: PatientAppointment) => (
                <span>{row.notes || '—'}</span>
            ),
        },
        {
            key: 'statut',
            title: t('Statut'),
            render: (row: PatientAppointment) => (
                <Badge variant={statusVariant[row.statut]}>{t(row.statut)}</Badge>
            ),
        },
    ];

    // Ajouter colonne actions si onCancel est fourni
    if (onCancel) {
        baseColumns.push({
            key: 'actions',
            title: t('Actions'),
            render: (row: PatientAppointment) =>
                row.statut === 'Confirmé' ? (
                    <Button
                        variant="danger"
                        size="small"
                        onClick={() => onCancel(row)}
                    >
                        {t('Annuler')}
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
