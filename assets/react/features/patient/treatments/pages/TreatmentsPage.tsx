import { useState } from 'react';
import { useTreatments } from '../hooks/useTreatments';
import { TreatmentsList } from '../components/TreatmentsList';
import { StopTreatmentModal } from '../components/StopTreatmentModal';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { Treatment } from '../types';
import '@/styles/pages/patient/treatments/_treatments.scss';

const HistoryIcon = () => (
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <circle cx="12" cy="12" r="10" />
        <polyline points="12 6 12 12 16 14" />
    </svg>
);

export function TreatmentsPage() {
    const { treatments, pastTreatments, stopTreatment, isLoading, error } = useTreatments();
    const [viewMode, setViewMode] = useState<'active' | 'history'>('active');
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const [stoppingTreatment, setStoppingTreatment] = useState<Treatment | null>(null);
    const [isStopping, setIsStopping] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    const toggleView = () => {
        setViewMode((prev) => (prev === 'active' ? 'history' : 'active'));
    };

    const handleStopRequest = (treatment: Treatment) => {
        setStoppingTreatment(treatment);
    };

    const confirmStop = async (reason?: string) => {
        if (!stoppingTreatment) return;
        setIsStopping(true);
        await stopTreatment(stoppingTreatment.prescriptionId, reason);
        setIsStopping(false);
        setStoppingTreatment(null);
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    const displayedTreatments = viewMode === 'active' ? treatments : pastTreatments;

    return (
        <div className="treatments-page">
            <div className="treatments-page__header">
                <h1>{viewMode === 'active' ? 'Mes traitements' : 'Historique des traitements'}</h1>
                <p>{viewMode === 'active' ? 'Ce qui vous est prescrit actuellement' : 'Traitements terminés ou arrêtés'}</p>
                <div className="treatments-page__header-actions">
                    <Button variant="secondary" onClick={openHelp}>Aide</Button>
                    <button
                        className="treatments-page__history-btn"
                        onClick={toggleView}
                        title={viewMode === 'active' ? 'Voir l’historique' : 'Voir les traitements actifs'}
                        aria-label="Basculer historique"
                    >
                        <HistoryIcon />
                    </button>
                </div>
            </div>

            <TreatmentsList
                treatments={displayedTreatments}
                isActiveView={viewMode === 'active'}
                onStopTreatment={handleStopRequest}
            />

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>Cette page liste vos traitements prescrits.</p>
                </Modal>
            )}

            <StopTreatmentModal
                isOpen={!!stoppingTreatment}
                onClose={() => setStoppingTreatment(null)}
                treatment={stoppingTreatment}
                onConfirm={confirmStop}
                isSubmitting={isStopping}
            />
        </div>
    );
}
