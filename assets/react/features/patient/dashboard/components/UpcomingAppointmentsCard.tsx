import { Card } from '@/react/components/UI/Card';
import { UpcomingAppointment } from '../types';

interface UpcomingAppointmentsCardProps {
    appointments: UpcomingAppointment[];
}

export function UpcomingAppointmentsCard({ appointments }: UpcomingAppointmentsCardProps) {
    if (appointments.length === 0) {
        return (
            <Card className="upcoming-appointments-card">
                <h3>Vos prochains rendez-vous</h3>
                <p className="upcoming-appointments-card__empty">Aucun rendez-vous à venir.</p>
            </Card>
        );
    }

    return (
        <Card className="upcoming-appointments-card">
            <h3>Vos prochains rendez-vous</h3>
            <ul>
                {appointments.map((appt) => (
                    <li key={appt.id} className="upcoming-appointments-card__item">
                        <div className="upcoming-appointments-card__date">
                            <strong>{appt.date}</strong>
                            <span>{appt.time}</span>
                        </div>
                        <div className="upcoming-appointments-card__info">
                            <strong>{appt.reason || 'Consultation'}</strong>
                            <span>{appt.doctor}</span>
                        </div>
                    </li>
                ))}
            </ul>
        </Card>
    );
}