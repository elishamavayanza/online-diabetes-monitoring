import { useClinicianNotifications } from '../hooks/useClinicianNotifications';
import { NotificationsTable } from '../components/NotificationsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { useI18n } from '@/react/i18n/I18nContext';
import '@/styles/pages/clinician/notifications/_notifications.scss';

export function ClinicianNotificationsPage() {
    const { t } = useI18n();
    const { notifications, filter, setFilter, isLoading, error } = useClinicianNotifications();
    const { pushAction } = useActionHistory();

    const tabs = [
        { id: 'Toutes', label: t('Toutes') },
        { id: 'Non lues', label: t('Non lues') },
    ];

    const handleFilterChange = (newFilter: string) => {
        const previousFilter = filter;
        setFilter(newFilter as typeof filter);
        pushAction(() => setFilter(previousFilter));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="clinician-notifications-page">
            <div className="clinician-notifications-page__header">
                <h1>{t('Notifications')}</h1>
                <p>{t('Vos alertes et rappels')}</p>
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
