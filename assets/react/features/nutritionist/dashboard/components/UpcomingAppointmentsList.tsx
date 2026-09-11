import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge/Badge';
import { useI18n } from '@/react/i18n/I18nContext';
import { UpcomingAppointment } from '../types';

interface UpcomingAppointmentsListProps {
    appointments: UpcomingAppointment[];
}

export function UpcomingAppointmentsList({ appointments }: UpcomingAppointmentsListProps) {
    const { t } = useI18n();
    return (
        <Card className="appointments-upcoming">
            <h2 className="section-title">{t('Prochaines consultations')}</h2>
            {appointments.length === 0 ? (
                <p className="appointments-upcoming__empty">{t('Aucun rendez-vous à venir.')}</p>
            ) : (
                <ul className="appointments-upcoming__items">
                    {appointments.map((appt) => (
                        <li key={appt.id} className="appointment-item">
                            <div className="appointment-item__main">
                                <span className="appointment-item__patient">{appt.patient}</span>
                                {appt.reason && <span className="appointment-item__reason">{appt.reason}</span>}
                            </div>
                            <div className="appointment-item__when">
                                {appt.isToday
                                    ? <Badge variant="success">{t("Aujourd'hui")}</Badge>
                                    : <span className="appointment-item__date">{t(appt.date)}</span>}
                                <span className="appointment-item__time">{appt.time}</span>
                            </div>
                        </li>
                    ))}
                </ul>
            )}
        </Card>
    );
}