import { useState } from 'react';
import { useNutritionistDashboard } from '../hooks/useNutritionistDashboard';
import { StatCard } from '../components/StatCard';
import { TodayAppointmentsList } from '../components/TodayAppointmentsList';
import { RecentActivityList } from '../components/RecentActivityList';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/nutritionist/dashboard/_dashboard.scss';

export function NutritionistDashboardPage() {
    const { data, isLoading, error } = useNutritionistDashboard();
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error || !data) return <Alert variant="error">{error ?? 'Aucune donnée disponible.'}</Alert>;

    return (
        <div className="nutritionist-dashboard-page">
            <div className="nutritionist-dashboard-page__header">
                <h1>Vue générale</h1>
                <p>Bienvenue, Nutritionniste Sarah</p>
                <Button variant="secondary" onClick={openHelp}>Aide</Button>
            </div>

            <div className="nutritionist-dashboard-page__stats">
                {data.stats.map((stat) => <StatCard key={stat.id} stat={stat} />)}
            </div>

            <div className="nutritionist-dashboard-page__grid">
                <TodayAppointmentsList appointments={data.appointmentsToday} />
                <RecentActivityList activities={data.recentActivities} />
            </div>

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>Tableau de bord résumant vos activités nutritionnelles.</p>
                </Modal>
            )}
        </div>
    );
}
