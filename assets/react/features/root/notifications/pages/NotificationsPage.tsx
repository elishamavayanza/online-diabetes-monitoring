import { useNotifications } from '../hooks/useNotifications';
import { NotificationsTable } from '../components/NotificationsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import '@/styles/pages/root/notifications/_notifications.scss';

export function NotificationsPage() {
    const { notifications, filter, setFilter, isLoading, error } = useNotifications();

    const tabs = [
        { id: 'Toutes', label: 'Toutes' },
        { id: 'Non lues', label: 'Non lues' },
        { id: 'Alertes système', label: 'Alertes système' },
    ];

    if (isLoading) {
        return <Spinner />;
    }

    if (error) {
        return <Alert variant="error">{error}</Alert>;
    }

    return (
        <div className="notifications-page">
            <div className="notifications-page__header">
                <h1>Notifications</h1>
                <p>Gérez les notifications système et les alertes</p>
            </div>

            <Tabs
                tabs={tabs}
                defaultActiveTabId={filter}
                onChange={(id) => setFilter(id as typeof filter)}
            />

            <NotificationsTable notifications={notifications} />
        </div>
    );
}
