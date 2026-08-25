import { Card } from '@/react/components/UI/Card';
import { DataTable } from '@/react/components/Data/DataTable';
import { AppointmentToday } from '../types';

interface TodayAppointmentsProps {
    appointments: AppointmentToday[];
}

export function TodayAppointments({ appointments }: TodayAppointmentsProps) {
    const columns = [
        { key: 'time', title: 'Heure' },
        { key: 'doctor', title: 'Professionnel' },
        { key: 'patient', title: 'Patient' },
    ];

    return (
        <Card className="appointments-card">
            <h2 className="section-title">Rendez-vous</h2>
            <DataTable columns={columns} data={appointments} />
        </Card>
    );
}
