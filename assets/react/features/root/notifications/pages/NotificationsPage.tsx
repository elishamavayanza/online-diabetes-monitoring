import { useState } from 'react';
import { useNotifications } from '../hooks/useNotifications';
import { NotificationsTable } from '../components/NotificationsTable';
import { NotificationDetailsModal } from '../components/NotificationDetailsModal';
import { PublishNotificationModal } from '../components/PublishNotificationModal';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import { Button } from '@/react/components/UI/Button';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { useI18n } from '@/react/i18n/I18nContext';
import '@/styles/pages/root/notifications/_notifications.scss';
import { Notification } from '../types';

export function NotificationsPage() {
    const { t } = useI18n();
    const { notifications, filter, setFilter, markAsRead, reload, isLoading, error } = useNotifications();
    const { pushAction } = useActionHistory();
    const [publishOpen, setPublishOpen] = useState(false);
    const [selectedNotification, setSelectedNotification] = useState<Notification | null>(null);
    const [detailsOpen, setDetailsOpen] = useState(false);

    const tabs = [
        { id: 'Toutes', label: t('Toutes') },
        { id: 'Non lues', label: t('Non lues') },
        { id: 'Alertes système', label: t('Alertes système') },
    ];

    const handleFilterChange = (newFilter: string) => {
        const previousFilter = filter;
        setFilter(newFilter as typeof filter);
        pushAction(() => setFilter(previousFilter));
    };

    const openPublishModal = () => {
        setPublishOpen(true);
        pushAction(() => setPublishOpen(false));
    };

    const openDetails = (notification: Notification) => {
        setSelectedNotification(notification);
        setDetailsOpen(true);
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="notifications-page">
            <div className="notifications-page__header">
                <h1>{t('Notifications')}</h1>
                <p>{t('Gérez les notifications système et les alertes')}</p>
            </div>

            <div className="notifications-page__actions">
                <Button variant="primary" onClick={openPublishModal}>
                    {t('Publier une alerte')}
                </Button>
            </div>

            <Tabs tabs={tabs} defaultActiveTabId={filter} onChange={handleFilterChange} />

            <NotificationsTable
                notifications={notifications}
                onNotificationClick={openDetails}
                onMarkAsRead={markAsRead}
            />

            <NotificationDetailsModal
                notification={selectedNotification}
                isOpen={detailsOpen}
                onClose={() => setDetailsOpen(false)}
            />

            <PublishNotificationModal
                isOpen={publishOpen}
                onClose={() => setPublishOpen(false)}
                onPublished={reload}
            />
        </div>
    );
}
