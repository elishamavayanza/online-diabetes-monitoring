import { useAppointments } from '../hooks/useAppointments';
import { AppointmentsTable } from '../components/AppointmentsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import { Button } from '@/react/components/UI/Button';
import '@/styles/pages/admin/appointments/_appointments.scss';

export function AppointmentsPage() {
    const { appointments, period, setPeriod, viewMode, setViewMode, isLoading, error } = useAppointments();

    const tabs = [
        { id: 'today', label: "Aujourd'hui" },
        { id: 'week', label: 'Cette semaine' },
        { id: 'month', label: 'Ce mois' },
    ];

    if (isLoading) {
        return <Spinner />;
    }

    if (error) {
        return <Alert variant="error">{error}</Alert>;
    }

    return (
        <div className="appointments-page">
            <div className="appointments-page__header">
                <h1>Rendez-vous</h1>
                <div>
                    <Button variant={viewMode === 'list' ? 'primary' : 'secondary'} onClick={() => setViewMode('list')}>
                        Liste
                    </Button>
                    <Button variant={viewMode === 'calendar' ? 'primary' : 'secondary'} onClick={() => setViewMode('calendar')}>
                        Calendrier
                    </Button>
                </div>
            </div>

            <Tabs
                tabs={tabs}
                defaultActiveTabId={period}
                onChange={(id) => setPeriod(id as typeof period)}
            />

            {viewMode === 'list' ? (
                <AppointmentsTable appointments={appointments} />
            ) : (
                <p style={{ textAlign: 'center', padding: '2rem' }}>Vue calendrier à implémenter</p>
            )}
        </div>
    );
}
