import { useAppointments } from '../hooks/useAppointments';
import { AppointmentsTable } from '../components/AppointmentsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import '@/styles/pages/clinician/appointments/_appointments.scss';

export function AppointmentPage() {
    const { appointments, filter, setFilter, isLoading, error } = useAppointments();

    const tabs = [
        { id: 'today', label: "Aujourd'hui" },
        { id: 'upcoming', label: 'À venir' },
        { id: 'completed', label: 'Terminés' },
        { id: 'cancelled', label: 'Annulés' },
    ];

    if (isLoading) {
        return <Spinner />;
    }

    if (error) {
        return <Alert variant="error">{error}</Alert>;
    }

    return (
        <div className="clinician-appointments-page">
            <div className="clinician-appointments-page__header">
                <h1>Rendez-vous</h1>
                <p>Gérez vos rendez-vous</p>
            </div>

            <Tabs
                tabs={tabs}
                defaultActiveTabId={filter}
                onChange={(id) => setFilter(id as typeof filter)}
            />

            <AppointmentsTable appointments={appointments} />
        </div>
    );
}
