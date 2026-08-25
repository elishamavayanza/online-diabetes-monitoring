import { Card } from '@/react/components/UI/Card';
import { AppointmentToday } from '../types';

interface TodayAppointmentsListProps {
    appointments: AppointmentToday[];
}

export function TodayAppointmentsList({ appointments }: TodayAppointmentsListProps) {
    return (
        <Card className="appointments-list">
            <h2 className="section-title">Rendez-vous du jour</h2>
            <ul className="appointments-list__items">
                {appointments.map((appt) => (
                    <li key={appt.id} className="appointment-item">
                        <span className="appointment-item__time">{appt.time}</span>
                        <span className="appointment-item__patient">{appt.patient}</span>
                    </li>
                ))}
            </ul>
        </Card>
    );
}
