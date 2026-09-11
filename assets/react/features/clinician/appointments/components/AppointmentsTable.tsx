import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { createAppointmentReminder } from '../services/appointmentsService';
import { Appointment } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

interface AppointmentsTableProps {
    appointments: Appointment[];
    onActionSuccess?: () => void; // pour rafraîchir éventuellement
}

const statusVariant: Record<string, 'success' | 'warning' | 'error' | 'primary'> = {
    'Confirmé': 'success',
    'En attente': 'warning',
    'Terminé': 'primary',
    'Annulé': 'error',
};

export function AppointmentsTable({ appointments, onActionSuccess }: AppointmentsTableProps) {
    const { t } = useI18n();
    const { showToast } = useToast();

    const handleReminder = async (appt: Appointment) => {
        try {
            // Date du rappel : 1 jour avant le rendez-vous à 09:00
            const appointmentDate = new Date(`${appt.date}T${appt.heure}`);
            const reminderDate = new Date(appointmentDate);
            reminderDate.setDate(reminderDate.getDate() - 1);
            reminderDate.setHours(9, 0, 0, 0);

            await createAppointmentReminder(
                appt.id,
                reminderDate.toISOString(),
                'SMS'
            );
            showToast({ type: 'success', message: t('Rappel programmé avec succès.') });
            onActionSuccess?.();
        } catch (err) {
            const message = err instanceof Error ? err.message : t('Erreur lors de la programmation.');
            showToast({ type: 'error', message });
        }
    };

    const columns = [
        { key: 'patient', title: t('Patient') },
        { key: 'date', title: t('Date') },
        { key: 'heure', title: t('Heure') },
        { key: 'motif', title: t('Motif') },
        {
            key: 'statut',
            title: t('Statut'),
            render: (row: Appointment) => (
                <Badge variant={statusVariant[row.statut]}>
                    {t(row.statut)}
                </Badge>
            ),
        },
        {
            key: 'actions',
            title: t('Actions'),
            render: (row: Appointment) => {
                const isUpcoming = row.statut === 'Confirmé' || row.statut === 'En attente';
                return isUpcoming ? (
                    <Button
                        size="small"
                        variant="secondary"
                        onClick={() => handleReminder(row)}
                    >
                        {t('Rappel')}
                    </Button>
                ) : null;
            },
        },
    ];

    return (
        <Card className="clinician-appointments-card">
            <DataTable columns={columns} data={appointments} />
        </Card>
    );
}
