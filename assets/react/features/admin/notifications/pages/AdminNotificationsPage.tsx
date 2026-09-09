import { useI18n } from '@/react/i18n/I18nContext';
import { useAdminNotifications } from '../hooks/useAdminNotifications';
import { NotificationsTable } from '../components/NotificationsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/admin/notifications/_notifications.scss';

export function AdminNotificationsPage() {
    const { t } = useI18n();
    const { notifications, filter, setFilter, markAsRead, isLoading, error } = useAdminNotifications();
    const { pushAction } = useActionHistory();

    const tabs = [
        { id: 'Toutes', label: t('Toutes') },
        { id: 'Non lues', label: t('Non lues') },
        { id: 'Système', label: t('Système') },
    ];

    const handleFilterChange = (newFilter: string) => {
        const previousFilter = filter;
        setFilter(newFilter as typeof filter);
        pushAction(() => setFilter(previousFilter));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="admin-notifications-page">
            <div className="admin-notifications-page__header">
                <h1>{t('Notifications')}</h1>
                <p>{t('Notifications de votre organisation')}</p>
            </div>

            <Tabs
                tabs={tabs}
                defaultActiveTabId={filter}
                onChange={handleFilterChange}
            />

            <NotificationsTable notifications={notifications} onMarkAsRead={markAsRead} />
        </div>
    );
}
