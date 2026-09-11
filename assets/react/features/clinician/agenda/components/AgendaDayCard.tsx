import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge/Badge';
import { AgendaDay, statusToBadgeVariant, statusLabel } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';

interface AgendaDayCardProps {
    day: AgendaDay;
}

export function AgendaDayCard({ day }: AgendaDayCardProps) {
    const { t } = useI18n();

    return (
        <Card className="agenda-day-card">
            <div className="agenda-day-card__header">
                <h3 className="agenda-day-card__label">{day.label}</h3>
                <span className="agenda-day-card__date">{day.date}</span>
            </div>
            {day.appointments.length === 0 ? (
                <p className="agenda-day-card__empty">{t('Aucun rendez-vous')}</p>
            ) : (
                <ul className="agenda-day-card__list">
                    {day.appointments.map((appt) => (
                        <li
                            key={appt.id}
                            className={`agenda-appointment ${appt.isPast ? 'agenda-appointment--past' : ''}`}
                        >
                            <span className="agenda-appointment__time">{appt.time}</span>
                            <div className="agenda-appointment__info">
                                <span className="agenda-appointment__patient">{appt.patient}</span>
                                <span className="agenda-appointment__motif">
                                    {appt.motif}
                                    {appt.durationMinutes ? ` · ${appt.durationMinutes} min` : ''}
                                </span>
                            </div>
                            {appt.status && (
                                <Badge variant={statusToBadgeVariant(appt.status)} size="small">
                                    {t(statusLabel(appt.status))}
                                </Badge>
                            )}
                            <span className={`agenda-appointment__type agenda-appointment__type--${appt.type === 'Consultation' ? 'consultation' : 'suivi'}`}>
                                {appt.type === 'Consultation' ? t('Consult.') : t('Suivi')}
                            </span>
                        </li>
                    ))}
                </ul>
            )}
        </Card>
    );
}