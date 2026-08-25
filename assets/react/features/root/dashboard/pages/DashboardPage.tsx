import { useDashboard } from '../hooks/useDashboard';
import { StatCard } from '../components/StatCard';
import { RecentActivityList } from '../components/RecentActivityList';
import { PlatformStatus } from '../components/PlatformStatus';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/root/dashboard/_dashboard.scss';
export function DashboardPage() {
    const { data, isLoading, error } = useDashboard();

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

            <div className="dashboard-page__stats">
                {data.stats.map((stat) => (
                    <StatCard key={stat.id} stat={stat} />
                ))}
            </div>

            <div className="dashboard-page__grid">
                <RecentActivityList activities={data.recentActivities} />
                <PlatformStatus items={data.platformStatus} />
            </div>
        </div>
    );
}
