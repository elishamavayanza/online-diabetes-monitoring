import { useState } from 'react';
import { useDashboard } from '../hooks/useDashboard';
import { StatCard } from '../components/StatCard';
import { RecentActivityList } from '../components/RecentActivityList';
import { PlatformStatus } from '../components/PlatformStatus';
import { Spinner } from '@/react/components/UI/Spinner';
import { ErrorState } from '@/react/components/UI/ErrorState';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { useI18n } from '@/react/i18n/I18nContext';
import '@/styles/pages/root/dashboard/_dashboard.scss';

export function DashboardPage() {
    const { t } = useI18n();
    const { data, isLoading, error, reload } = useDashboard();
    const [modalOpen, setModalOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openModal = () => {
        setModalOpen(true);
        // Enregistre l'action inverse : fermer la modale
        pushAction(() => setModalOpen(false));
    };

    if (isLoading) {
        return <Spinner />;
    }

    if (error || !data) {
        return error ? (
            <ErrorState size="full" {...error} onRetry={reload} />
        ) : (
            <ErrorState
                size="full"
                title={t('Aucune donnée disponible')}
                message={t('Le tableau de bord est vide pour le moment.')}
                onRetry={reload}
            />
        );
    }

    return (
        <div className="dashboard-page">
            <div className="dashboard-page__header">
                <h1>{t('Bonjour, Administrateur')}</h1>
                <p>{t('Vue générale de la plateforme')}</p>
            </div>

            <Button onClick={openModal}>{t('Ouvrir une action')}</Button>

            <div className="dashboard-page__stats">
                {data.stats.map((stat) => (
                    <StatCard key={stat.id} stat={stat} />
                ))}
            </div>

            <div className="dashboard-page__grid">
                <RecentActivityList activities={data.recentActivities} />
                <PlatformStatus items={data.platformStatus} />
            </div>

            <Modal
                isOpen={modalOpen}
                onClose={() => setModalOpen(false)}
            >
                <p>{t('Ceci est une modale. Utilisez le bouton retour pour la fermer d\u2019abord.')}</p>
            </Modal>
        </div>
    );
}
