import { usePatientNotifications } from '../hooks/usePatientNotifications';
import { NotificationsTable } from '../components/NotificationsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/patient/notifications/_notifications.scss';

export function PatientNotificationsPage() {
    const { notifications, filter, setFilter, isLoading, error } = usePatientNotifications();
    const { pushAction } = useActionHistory();

    const tabs = [
        { id: 'Toutes', label: 'Toutes' },
        { id: 'Non lues', label: 'Non lues' },
    ];

    const handleFilterChange = (newFilter: string) => {
        const previousFilter = filter;
        setFilter(newFilter as typeof filter);
        // Action inverse : restaurer l'ancien filtre
        pushAction(() => setFilter(previousFilter));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="notifications-page">
            <div className="notifications-page__header">
                <h1>Notifications</h1>
                <p>Vos alertes et rappels</p>
            </div>
            <Tabs
                tabs={tabs}
                defaultActiveTabId={filter}
                onChange={handleFilterChange}
            />
            <NotificationsTable notifications={notifications} />
        </div>
    );
}
