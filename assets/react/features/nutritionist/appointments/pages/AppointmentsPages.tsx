import { useAppointments } from '../hooks/useAppointments';
import { AppointmentsTable } from '../components/AppointmentsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/nutritionist/appointments/_appointments.scss';

export function AppointmentsPages() {
    const { appointments, filter, setFilter, isLoading, error, reload } = useAppointments();
    const { pushAction } = useActionHistory();

    const tabs = [
        { id: 'today', label: "Aujourd'hui" },
        { id: 'upcoming', label: 'À venir' },
        { id: 'completed', label: 'Terminés' },
        { id: 'cancelled', label: 'Annulés' },
    ];

    const handleFilterChange = (newFilter: string) => {
        const previousFilter = filter;
        setFilter(newFilter as typeof filter);
        pushAction(() => setFilter(previousFilter));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="appointments-page">
            <div className="appointments-page__header">
                <h1>Rendez-vous</h1>
                <p>Gérez vos rendez-vous</p>
            </div>
            <Tabs
                tabs={tabs}
                defaultActiveTabId={filter}
                onChange={handleFilterChange}
            />
            <AppointmentsTable appointments={appointments} onActionSuccess={reload} />
        </div>
    );
}
