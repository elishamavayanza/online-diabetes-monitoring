// AppointmentsPage.tsx
import { useState, useMemo } from 'react';
import { useAppointments } from '../hooks/useAppointments';
import { AppointmentsTable } from '../components/AppointmentsTable';
import { AppointmentsCalendar } from '../components/AppointmentsCalendar';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import { Button } from '@/react/components/UI/Button';
import { RightSidebar } from '@/react/components/Navigation/RightSidebar';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { AppointmentsCreateModal } from '../components/AppointmentsCreateModal';
import { AppointmentsEditModal } from '@/react/features/admin/appointments/components/AppointmentsEditModal';
import { Appointment } from '../types/types';
import '@/styles/pages/admin/appointments/_appointments.scss';

const toDateKey = (date: Date): string => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

export function AppointmentsPage() {
    const {allAppointments, appointments, period, setPeriod, isLoading, error } = useAppointments();
    const { pushAction } = useActionHistory();
    const [selectedDate, setSelectedDate] = useState<Date | null>(null);
    const [isCreateModalOpen, setIsCreateModalOpen] = useState(false);
    const [editingAppointment, setEditingAppointment] = useState<Appointment | null>(null);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);

    // Ajout de l'onglet historique
    const tabs = [
        { id: 'today', label: "Aujourd'hui" },
        { id: 'week', label: 'Cette semaine' },
        { id: 'month', label: 'Ce mois' },
        { id: 'history', label: 'Historique' },
    ];

    // Les rendez-vous passés ne sont pas affichés dans les autres onglets,
    // car le service les filtre déjà. Pour l'onglet historique, on affiche tous ceux retournés.
    // La table doit recevoir `showActions={period !== 'history'}`.

    const filteredAppointments = useMemo(() => {
        if (!selectedDate) return appointments;
        const selectedKey = toDateKey(selectedDate);
        return appointments.filter((appt) => appt.date === selectedKey);
    }, [appointments, selectedDate]);

    const handlePeriodChange = (newPeriod: string) => {
        const previousPeriod = period;
        setPeriod(newPeriod as typeof period);
        setSelectedDate(null);
        pushAction(() => setPeriod(previousPeriod));
    };

    const handleNewAppointmentClick = () => setIsCreateModalOpen(true);
    const handleCloseCreateModal = () => setIsCreateModalOpen(false);
    const handleCreatedAppointment = (appointment: Appointment) => {
        console.log('Rendez-vous créé :', appointment);
    };

    const handleEditAppointment = (appointment: Appointment) => {
        setEditingAppointment(appointment);
        setIsEditModalOpen(true);
    };
    const handleCloseEditModal = () => {
        setIsEditModalOpen(false);
        setEditingAppointment(null);
    };
    const handleUpdatedAppointment = (updated: Appointment) => {
        console.log('Rendez-vous mis à jour :', updated);
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="appointments-page">
            <div className="appointments-page__header">
                <h1>Rendez-vous</h1>
                <p>Gérez les rendez-vous de votre organisation</p>
            </div>

            <div className="appointments-page__actions">
                <Button variant="primary" onClick={handleNewAppointmentClick}>
                    + Nouveau rendez-vous
                </Button>
            </div>

            <Tabs
                tabs={tabs}
                defaultActiveTabId={period}
                onChange={handlePeriodChange}
            />

            <div className="appointments-page__body">
                <div className="appointments-page__content">
                    <AppointmentsTable
                        appointments={filteredAppointments}
                        onEdit={handleEditAppointment}
                        onCancel={(appt) => console.log('Annuler', appt)}
                        onRemind={(appt) => console.log('Rappeler', appt)}
                        showActions={period !== 'history'} // masquer les actions en historique
                    />
                </div>

                <RightSidebar
                    collapsible
                    size="medium"
                    minWidth={250}
                    maxWidth={400}
                    closeThreshold={80}
                    collapsedWidth={35}
                    title="Calendrier"
                    header={<div> Choisissez une date</div>}
                >
                    <div className="appointments-page__right-content">
                        <AppointmentsCalendar
                            appointments={allAppointments}
                            selectedDate={selectedDate}
                            onDateSelect={setSelectedDate}
                        />
                    </div>
                </RightSidebar>
            </div>

            <AppointmentsCreateModal
                isOpen={isCreateModalOpen}
                onClose={handleCloseCreateModal}
                onCreated={handleCreatedAppointment}
            />

            <AppointmentsEditModal
                isOpen={isEditModalOpen}
                onClose={handleCloseEditModal}
                appointment={editingAppointment}
                onUpdated={handleUpdatedAppointment}
            />
        </div>
    );
}
