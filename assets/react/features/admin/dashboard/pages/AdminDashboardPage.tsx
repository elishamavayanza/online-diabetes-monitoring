import { useAdminDashboard } from '../hooks/useAdminDashboard';
import { StatCard } from '../components/StatCard';
import { RecentActivityList } from '../components/RecentActivityList';
import { TodayAppointments } from '../components/TodayAppointments';
import { OrganizationStatus } from '../components/OrganizationStatus';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/admin/dashboard/_dashboard.scss';

export function AdminDashboardPage() {
    const { data, isLoading, error } = useAdminDashboard();

    if (isLoading) {
        return <Spinner />;
    }

    if (error || !data) {
        return <Alert variant="error">{error ?? 'Aucune donnée disponible.'}</Alert>;
    }

    return (
        <div className="admin-dashboard-page">
            <div className="admin-dashboard-page__header">
                <h1>Vue générale</h1>
                <p>Tableau de bord de votre organisation</p>
            </div>

            <div className="admin-dashboard-page__stats">
                {data.stats.map((stat) => (
                    <StatCard key={stat.id} stat={stat} />
                ))}
            </div>

            <div className="admin-dashboard-page__grid">
                <RecentActivityList activities={data.recentActivities} />
                <OrganizationStatus items={data.organizationStatus} />
            </div>

            <TodayAppointments appointments={data.appointmentsToday} />
        </div>
    );
}
