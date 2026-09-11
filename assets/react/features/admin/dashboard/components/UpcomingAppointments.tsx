import { useI18n } from '@/react/i18n/I18nContext';
import { Card } from '@/react/components/UI/Card';
import { UpcomingAppointment } from '../types';

interface UpcomingAppointmentsProps {
    appointments: UpcomingAppointment[];
}

export function UpcomingAppointments({ appointments }: UpcomingAppointmentsProps) {
    const { t } = useI18n();

    return (
        <Card className="appointments-card">
            <h2 className="section-title">{t('Prochains rendez-vous')}</h2>
            {appointments.length === 0 ? (
                <p className="appointments-card__empty">{t('Aucun rendez-vous à venir.')}</p>
            ) : (
                <ul className="appointments-card__list">
                    {appointments.map((appt) => (
                        <li key={appt.id} className="appointments-card__item">
                            <div className="appointments-card__main">
                                <span className="appointments-card__patient">{appt.patient}</span>
                                <span className="appointments-card__doctor">{appt.doctor}</span>
                            </div>
                            <div className="appointments-card__when">
                                <span className="appointments-card__date">{appt.date}</span>
                                <span className="appointments-card__time">{appt.time}</span>
                            </div>
                        </li>
                    ))}
                </ul>
            )}
        </Card>
    );
}
