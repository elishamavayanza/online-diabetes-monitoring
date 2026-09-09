import { useI18n } from '@/react/i18n/I18nContext';
import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { AppointmentToday } from '../types';

interface TodayAppointmentsProps {
    appointments: AppointmentToday[];
}

export function TodayAppointments({ appointments }: TodayAppointmentsProps) {
    const { t } = useI18n();

    const columns = [
        { key: 'time', title: t('Heure') },
        { key: 'doctor', title: t('Professionnel') },
        { key: 'patient', title: t('Patient') },
    ];

    return (
        <Card className="appointments-card">
            <h2 className="section-title">{t('Rendez-vous')}</h2>
            <DataTable columns={columns} data={appointments} />
        </Card>
    );
}
