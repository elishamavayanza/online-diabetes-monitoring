import { useState } from 'react';
import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import {formatDisplayDateTime, getAppointmentStatusBadgeVariant, isInPeriod} from '../../utils/dossierUtils';
import {AppointmentEditModal} from "@/react/features/clinician/patients/components/modals/AppointmentEditModal";
import { PatientAppointment } from '../../types';

export function AppointmentsTab() {
    const { data, period, selectedDate, isReadOnly, openAppointmentModal, reload } =
        usePatientDossierContext();

    const [editingAppointment, setEditingAppointment] = useState<PatientAppointment | null>(null);

    const appointments = data.appointments
        .filter((appt) => isInPeriod(appt.scheduledAt, period, selectedDate))
        .sort((a, b) => new Date(b.scheduledAt).getTime() - new Date(a.scheduledAt).getTime());

    return (
        <div className="patient-dossier-tab patient-dossier-tab--appointments">
            <div className="patient-dossier-tab__toolbar">
                <p className="patient-dossier-tab__hint">Rendez-vous planifiés et passés.</p>
                {!isReadOnly && (
                    <Button variant="primary" onClick={openAppointmentModal}>
                        + Nouveau rendez-vous
                    </Button>
                )}
            </div>

            {appointments.length === 0 ? (
                <Card>
                    <p>Aucun rendez-vous sur la période sélectionnée.</p>
                </Card>
            ) : (
                <div className="patient-dossier-tab__grid">
                    {appointments.map((appt) => (
                        <Card key={appt.id}>
                            <div className="patient-dossier-tab__card-header">
                                <h3>{formatDisplayDateTime(appt.scheduledAt)}</h3>
                                <Badge variant={getAppointmentStatusBadgeVariant(appt.status)}>
                                    {appt.status}
                                </Badge>
                            </div>
                            {appt.reason && (
                                <p>
                                    <strong>Motif :</strong> {appt.reason}
                                </p>
                            )}
                            {appt.durationMinutes != null && (
                                <p>
                                    <strong>Durée :</strong> {appt.durationMinutes} min
                                </p>
                            )}

                            {!isReadOnly && (
                                <div className="patient-dossier-tab__item-actions">
                                    <Button
                                        variant="secondary"
                                        size="small"
                                        onClick={() => setEditingAppointment(appt)}
                                    >
                                        Modifier
                                    </Button>
                                </div>
                            )}
                        </Card>
                    ))}
                </div>
            )}

            {editingAppointment && (
                <AppointmentEditModal
                    isOpen={!!editingAppointment}
                    onClose={() => setEditingAppointment(null)}
                    data={data}
                    appointment={editingAppointment}
                    onSuccess={reload}
                />
            )}
        </div>
    );
}
