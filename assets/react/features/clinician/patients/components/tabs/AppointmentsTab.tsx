import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { formatDisplayDateTime, isInPeriod } from '../../utils/dossierUtils';

export function AppointmentsTab() {
    const { data, period, selectedDate, isReadOnly, openAppointmentModal } = usePatientDossierContext();

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
                <Card><p>Aucun rendez-vous sur la période sélectionnée.</p></Card>
            ) : (
                <div className="patient-dossier-tab__grid">
                    {appointments.map((appt) => (
                        <Card key={appt.id}>
                            <div className="patient-dossier-tab__card-header">
                                <h3>{formatDisplayDateTime(appt.scheduledAt)}</h3>
                                <Badge variant="info">{appt.status}</Badge>
                            </div>
                            {appt.reason && <p><strong>Motif :</strong> {appt.reason}</p>}
                            {appt.durationMinutes != null && <p><strong>Durée :</strong> {appt.durationMinutes} min</p>}
                        </Card>
                    ))}
                </div>
            )}
        </div>
    );
}
