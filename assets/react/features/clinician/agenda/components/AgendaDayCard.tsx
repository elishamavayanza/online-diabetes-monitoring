import { Card } from '@/react/components/UI/Card';
import { AgendaDay } from '../types';

interface AgendaDayCardProps {
    day: AgendaDay;
}

export function AgendaDayCard({ day }: AgendaDayCardProps) {
    return (
        <Card className="agenda-day-card">
            <div className="agenda-day-card__header">
                <h3 className="agenda-day-card__label">{day.label}</h3>
                <span className="agenda-day-card__date">{day.date}</span>
            </div>
            {day.appointments.length === 0 ? (
                <p className="agenda-day-card__empty">Aucun rendez-vous</p>
            ) : (
                <ul className="agenda-day-card__list">
                    {day.appointments.map((appt) => (
                        <li key={appt.id} className="agenda-appointment">
                            <span className="agenda-appointment__time">{appt.time}</span>
                            <div className="agenda-appointment__info">
                                <span className="agenda-appointment__patient">{appt.patient}</span>
                                <span className="agenda-appointment__motif">{appt.motif}</span>
                            </div>
                            <span className={`agenda-appointment__type agenda-appointment__type--${appt.type === 'Consultation' ? 'consultation' : 'suivi'}`}>
                                {appt.type}
                            </span>
                        </li>
                    ))}
                </ul>
            )}
        </Card>
    );
}
