import { useState } from 'react';
import { usePatientDashboard } from '../hooks/usePatientDashboard';
import { HealthSummaryCard } from '../components/HealthSummaryCard';
import { NextAppointmentCard } from '../components/NextAppointmentCard';
import { WatchList } from '../components/WatchList';
import { UpcomingAppointmentsCard } from '../components/UpcomingAppointmentsCard';
import { ActiveTreatmentsCard } from '../components/ActiveTreatmentsCard';
import { RecentNotesCard } from '../components/RecentNotesCard';
import { Spinner } from '@/react/components/UI/Spinner';
import { ErrorState } from '@/react/components/UI/ErrorState';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { useI18n } from '@/react/i18n/I18nContext';
import '@/styles/pages/patient/dashboard/_dashboard.scss';

export function PatientDashboardPage() {
    const { t } = useI18n();
    const { data, isLoading, error, reload } = usePatientDashboard();
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
                message={t('Le résumé de santé est vide pour le moment.')}
                onRetry={reload}
            />
        );
    }

    return (
        <div className="patient-dashboard-page">
            <div className="patient-dashboard-page__header">
                <h1>{t('Bonjour {{ name }}', { name: data.patientName })}</h1>
                <Button variant="secondary" onClick={openHelp}>{t('Aide')}</Button>
            </div>

            <HealthSummaryCard metrics={data.metrics} />

            <div className="patient-dashboard-page__grid">
                <WatchList items={data.watchList} />
                <NextAppointmentCard appointment={data.nextAppointment} medication={data.nextMedication} />
            </div>

            <div className="patient-dashboard-page__grid">
                <UpcomingAppointmentsCard appointments={data.upcomingAppointments} />
                <ActiveTreatmentsCard treatments={data.treatments} />
                <RecentNotesCard notes={data.recentNotes} />
            </div>

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>{t('Ceci est votre espace de suivi : dernières mesures, rendez-vous à venir, traitements en cours et notes médicales récentes.')}</p>
                </Modal>
            )}
        </div>
    );
}