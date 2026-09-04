import { useState } from 'react';
import { Tabs } from '@/react/components/Navigation/Tabs';
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
    const [appointmentTab, setAppointmentTab] = useState<'today' | 'pending' | 'confirmed'>('today');
    const [isRequestModalOpen, setIsRequestModalOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const today = new Date();
    const todayStr = today.toDateString();

    const filteredAppointments = appointments.filter((appt) => {
        const appointmentDate = new Date(appt.scheduledAt);
        const isPast = appointmentDate < new Date();
        const isCancelled = appt.statut === 'Annulé';

        if (viewMode === 'history') {
            // Historique = passés ou annulés
            return isPast || isCancelled;
        }

        // Vue "Mes rendez-vous" : uniquement non passés et non annulés
        if (isPast || isCancelled) return false;

        // Si l'onglet actif est "Aujourd'hui"
        if (appointmentTab === 'today') {
            return appointmentDate.toDateString() === todayStr;
        }

        // Sinon selon le statut
        if (appointmentTab === 'pending') {
            return appt.statut === 'En attente' || appt.statut === 'Report demandé';
        }
        if (appointmentTab === 'confirmed') {
            return appt.statut === 'Confirmé';
        }
        return true;
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

    const tabs = [
        { id: 'today', label: "Aujourd'hui" },
        { id: 'pending', label: 'En attente' },
        { id: 'confirmed', label: 'Confirmés' },
    ];

    return (
        <div className="patient-appointments-page">
            <div className="patient-appointments-page__header">
                <h1>{viewMode === 'upcoming' ? 'Mes rendez-vous' : 'Historique des rendez-vous'}</h1>

                {viewMode === 'upcoming' && (
                    <Tabs
                        tabs={tabs}
                        defaultActiveTabId={appointmentTab}
                        onChange={(id) => setAppointmentTab(id as 'today' | 'pending' | 'confirmed')}
                    />
                )}

                <p>
                    {viewMode === 'upcoming'
                        ? 'Vos prochaines consultations'
                        : 'Rendez-vous passés ou annulés'}
                </p>

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
                    reload();
                }}
            />
        </div>
    );
}
