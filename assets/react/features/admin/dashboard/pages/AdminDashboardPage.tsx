import { useState } from 'react';
import { useI18n } from '@/react/i18n/I18nContext';
import { useAdminDashboard } from '../hooks/useAdminDashboard';
import { StatCard } from '../components/StatCard';
import { RecentActivityList } from '../components/RecentActivityList';
import { TodayAppointments } from '../components/TodayAppointments';
import { UpcomingAppointments } from '../components/UpcomingAppointments';
import { OrganizationStatus } from '../components/OrganizationStatus';
import { Spinner } from '@/react/components/UI/Spinner';
import { ErrorState } from '@/react/components/UI/ErrorState';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/admin/dashboard/_dashboard.scss';

export function AdminDashboardPage() {
    const { t } = useI18n();
    const { data, isLoading, error, reload } = useAdminDashboard();
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    if (isLoading) return <Spinner />;
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
        <div className="admin-dashboard-page">
            <div className="admin-dashboard-page__header">
                <h1>{t('Vue générale')}</h1>
                <p>{t('Tableau de bord de votre organisation')}</p>
                <Button variant="secondary" onClick={openHelp}>{t('Aide')}</Button>
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

            <div className="admin-dashboard-page__grid">
                <TodayAppointments appointments={data.appointmentsToday} />
                <UpcomingAppointments appointments={data.upcomingAppointments} />
            </div>

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>{t("Vue d'ensemble de votre organisation : patients, professionnels, établissements et rendez-vous.")}</p>
                </Modal>
            )}
        </div>
    );
}
