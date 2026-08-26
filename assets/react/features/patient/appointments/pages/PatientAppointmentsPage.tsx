import { useState } from 'react';
import { usePatientAppointments } from '../hooks/usePatientAppointments';
import { PatientAppointmentsTable } from '../components/PatientAppointmentsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/patient/appointments/_appointments.scss';

export function PatientAppointmentsPage() {
    const { appointments, isLoading, error } = usePatientAppointments();
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="patient-appointments-page">
            <div className="patient-appointments-page__header">
                <h1>Mes rendez-vous</h1>
                <p>Vos prochaines consultations</p>
                <Button variant="secondary" onClick={openHelp}>Aide</Button>
            </div>
            <PatientAppointmentsTable appointments={appointments} />

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>Ici s'affichent vos rendez-vous à venir et passés.</p>
                </Modal>
            )}
        </div>
    );
}
