import { useState } from 'react';
import { useClinicianDashboard } from '../hooks/useClinicianDashboard';
import { StatCard } from '../components/StatCard';
import { TodayAppointmentsList } from '../components/TodayAppointmentsList';
import { UpcomingAppointmentsList } from '../components/UpcomingAppointmentsList';
import { FollowUpList } from '../components/FollowUpList';
import { RecentActivityList } from '../components/RecentActivityList';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { useAuth } from '@/react/app/providers/AuthProvider';
import '@/styles/pages/clinician/dashboard/_dashboard.scss';

export function ClinicianDashboardPage() {
    const { data, isLoading, error } = useClinicianDashboard();
    const { user } = useAuth();
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error || !data) return <Alert variant="error">{error ?? 'Aucune donnée disponible.'}</Alert>;

    return (
        <div className="clinician-dashboard-page">
            <div className="clinician-dashboard-page__header">
                <h1>Vue générale</h1>
                <p>{user?.name ? `Bienvenue, ${user.name}` : 'Bienvenue'}</p>
                <Button variant="secondary" onClick={openHelp}>Aide</Button>
            </div>

            <div className="clinician-dashboard-page__stats">
                {data.stats.map((stat) => (
                    <StatCard key={stat.id} stat={stat} />
                ))}
            </div>

            <div className="clinician-dashboard-page__grid">
                <TodayAppointmentsList appointments={data.appointmentsToday} />
                <UpcomingAppointmentsList appointments={data.upcomingAppointments} />
            </div>

            <div className="clinician-dashboard-page__grid">
                <RecentActivityList activities={data.recentActivities} />
                <FollowUpList patients={data.followUpPatients} />
            </div>

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>Tableau de bord résumant vos rendez-vous, vos patients et les suivis à planifier.</p>
                </Modal>
            )}
        </div>
    );
}
