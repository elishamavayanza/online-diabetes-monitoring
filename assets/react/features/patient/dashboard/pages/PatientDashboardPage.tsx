import { useState } from 'react';
import { usePatientDashboard } from '../hooks/usePatientDashboard';
import { HealthSummaryCard } from '../components/HealthSummaryCard';
import { NextAppointmentCard } from '../components/NextAppointmentCard';
import { WatchList } from '../components/WatchList';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/patient/dashboard/_dashboard.scss';

export function PatientDashboardPage() {
    const { data, isLoading, error } = usePatientDashboard();
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error || !data) return <Alert variant="error">{error ?? 'Aucune donnée'}</Alert>;

    return (
        <div className="patient-dashboard-page">
            <div className="patient-dashboard-page__header">
                <h1>Bonjour {data.patientName}</h1>
                <Button variant="secondary" onClick={openHelp}>Aide</Button>
            </div>

            <div className="patient-dashboard-page__grid">
                <HealthSummaryCard metrics={data.metrics} />
                <NextAppointmentCard appointment={data.nextAppointment} medication={data.nextMedication} />
                <WatchList items={data.watchList} />
            </div>

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>Ceci est votre résumé de santé : glycémie, poids, HbA1c, rendez-vous et prises.</p>
                </Modal>
            )}
        </div>
    );
}
