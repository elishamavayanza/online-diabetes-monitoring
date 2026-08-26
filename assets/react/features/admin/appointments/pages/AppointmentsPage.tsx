import { useAppointments } from '../hooks/useAppointments';
import { AppointmentsTable } from '../components/AppointmentsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import { Button } from '@/react/components/UI/Button';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/admin/appointments/_appointments.scss';

export function AppointmentsPage() {
    const { appointments, period, setPeriod, viewMode, setViewMode, isLoading, error } = useAppointments();
    const { pushAction } = useActionHistory();

    const tabs = [
        { id: 'today', label: "Aujourd'hui" },
        { id: 'week', label: 'Cette semaine' },
        { id: 'month', label: 'Ce mois' },
    ];

    const handlePeriodChange = (newPeriod: string) => {
        const previousPeriod = period;
        setPeriod(newPeriod as typeof period);
        pushAction(() => setPeriod(previousPeriod));
    };

    const handleViewModeChange = (newMode: 'list' | 'calendar') => {
        const previousMode = viewMode;
        setViewMode(newMode);
        pushAction(() => setViewMode(previousMode));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="appointments-page">
            <div className="appointments-page__header">
                <h1>Rendez-vous</h1>
                <div>
                    <Button variant={viewMode === 'list' ? 'primary' : 'secondary'} onClick={() => handleViewModeChange('list')}>
                        Liste
                    </Button>
                    <Button variant={viewMode === 'calendar' ? 'primary' : 'secondary'} onClick={() => handleViewModeChange('calendar')}>
                        Calendrier
                    </Button>
                </div>
            </div>

            <Tabs
                tabs={tabs}
                defaultActiveTabId={period}
                onChange={handlePeriodChange}
            />

            {viewMode === 'list' ? (
                <AppointmentsTable appointments={appointments} />
            ) : (
                <p style={{ textAlign: 'center', padding: '2rem' }}>Vue calendrier à implémenter</p>
            )}
        </div>
    );
}
