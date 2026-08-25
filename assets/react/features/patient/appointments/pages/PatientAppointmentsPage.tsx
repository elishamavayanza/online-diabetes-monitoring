import { usePatientAppointments } from '../hooks/usePatientAppointments';
import { PatientAppointmentsTable } from '../components/PatientAppointmentsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/patient/appointments/_appointments.scss';

export function PatientAppointmentsPage() {
    const { appointments, isLoading, error } = usePatientAppointments();

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="patient-appointments-page">
            <div className="patient-appointments-page__header">
                <h1>Mes rendez-vous</h1>
                <p>Vos prochaines consultations</p>
            </div>
            <PatientAppointmentsTable appointments={appointments} />
        </div>
    );
}
