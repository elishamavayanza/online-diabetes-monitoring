import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge/Badge';
import { UpcomingAppointment } from '../types';

interface UpcomingAppointmentsListProps {
    appointments: UpcomingAppointment[];
}

export function UpcomingAppointmentsList({ appointments }: UpcomingAppointmentsListProps) {
    return (
        <Card className="appointments-upcoming">
            <h2 className="section-title">Prochaines consultations</h2>
            {appointments.length === 0 ? (
                <p className="appointments-upcoming__empty">Aucun rendez-vous à venir.</p>
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
                                    ? <Badge variant="success">Aujourd'hui</Badge>
                                    : <span className="appointment-item__date">{appt.date}</span>}
                                <span className="appointment-item__time">{appt.time}</span>
                            </div>
                        </li>
                    ))}
                </ul>
            )}
        </Card>
    );
}