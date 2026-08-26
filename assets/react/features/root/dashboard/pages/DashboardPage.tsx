import { useState } from 'react';
import { useDashboard } from '../hooks/useDashboard';
import { StatCard } from '../components/StatCard';
import { RecentActivityList } from '../components/RecentActivityList';
import { PlatformStatus } from '../components/PlatformStatus';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/root/dashboard/_dashboard.scss';

export function DashboardPage() {
    const { data, isLoading, error } = useDashboard();
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
        return <Alert variant="error">{error ?? 'Aucune donnée disponible.'}</Alert>;
    }

    return (
        <div className="dashboard-page">
            <div className="dashboard-page__header">
                <h1>Bonjour, Administrateur</h1>
                <p>Vue générale de la plateforme</p>
            </div>

            <Button onClick={openModal}>Ouvrir une action</Button>

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
                <p>Ceci est une modale. Utilisez le bouton retour pour la fermer d'abord.</p>
            </Modal>
        </div>
    );
}
