import { useState } from 'react';
import { usePatientAppointments } from '../hooks/usePatientAppointments';
import { PatientAppointmentsTable } from '../components/PatientAppointmentsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { RequestAppointmentModal } from '../components/RequestAppointmentModal';
import '@/styles/pages/patient/appointments/_appointments.scss';

const HistoryIcon = () => (
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <circle cx="12" cy="12" r="10" />
        <polyline points="12 6 12 12 16 14" />
    </svg>
);

export function PatientAppointmentsPage() {
    const { appointments, isLoading, error, reload } = usePatientAppointments();
    const [viewMode, setViewMode] = useState<'upcoming' | 'history'>('upcoming');
    const [isRequestModalOpen, setIsRequestModalOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const filteredAppointments = appointments.filter((appt) => {
        const appointmentDate = new Date(appt.date);
        const isPast = appointmentDate < new Date();
        const isCancelled = appt.statut === 'Annulé';

        if (viewMode === 'upcoming') {
            return !isPast && !isCancelled;
        }
        return isPast || isCancelled;
    });

    const toggleViewMode = () => {
        const previousMode = viewMode;
        setViewMode(previousMode === 'upcoming' ? 'history' : 'upcoming');
        pushAction(() => setViewMode(previousMode));
    };

    const handleRequestAppointment = () => {
        setIsRequestModalOpen(true);
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="patient-appointments-page">
            <div className="patient-appointments-page__header">
                {/* Titre centré */}
                <h1>{viewMode === 'upcoming' ? 'Mes rendez-vous' : 'Historique des rendez-vous'}</h1>

                {/* Paragraphe */}
                <p>
                    {viewMode === 'upcoming'
                        ? 'Vos prochaines consultations'
                        : 'Rendez-vous passés ou annulés'}
                </p>

                {/* Boutons : principal pleine largeur + icône historique à droite */}
                <div className="patient-appointments-page__actions-row">
                    <Button
                        variant="primary"
                        onClick={handleRequestAppointment}
                        className="patient-appointments-page__request-btn"
                    >
                        Demander un rendez-vous
                    </Button>
                    <button
                        className="patient-appointments-page__history-btn"
                        onClick={toggleViewMode}
                        title={viewMode === 'upcoming' ? 'Voir l’historique' : 'Voir les rendez-vous à venir'}
                        aria-label="Basculer l’affichage"
                    >
                        <HistoryIcon />
                    </button>
                </div>
            </div>

            <PatientAppointmentsTable appointments={filteredAppointments} />

            <RequestAppointmentModal
                isOpen={isRequestModalOpen}
                onClose={() => setIsRequestModalOpen(false)}
                onSuccess={() => {
                    setIsRequestModalOpen(false);
                    reload(); // ✅ recharge les rendez-vous après création
                }}
            />
        </div>
    );
}
