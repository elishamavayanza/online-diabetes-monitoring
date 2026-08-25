import { Card } from '@/react/components/UI/Card';
import { AgendaDay } from '../types';

interface AgendaDayCardProps {
    day: AgendaDay;
}

export function AgendaDayCard({ day }: AgendaDayCardProps) {
    return (
        <Card className="agenda-day-card">
            <div className="agenda-day-card__header">
                <h3>{day.label}</h3>
                <span>{day.date}</span>
            </div>
            {day.appointments.length === 0 ? (
                <p className="agenda-day-card__empty">Aucun rendez-vous</p>
            ) : (
                <ul className="agenda-day-card__list">
                    {day.appointments.map((appt) => (
                        <li key={appt.id} className="agenda-appointment">
                            <span className="agenda-appointment__time">{appt.time}</span>
                            <div>
                                <span className="agenda-appointment__patient">{appt.patient}</span>
                                <span className="agenda-appointment__motif">{appt.motif}</span>
                            </div>
                        </li>
                    ))}
                </ul>
            )}
        </Card>
    );
}
