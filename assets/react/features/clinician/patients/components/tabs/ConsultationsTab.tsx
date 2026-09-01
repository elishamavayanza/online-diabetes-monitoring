import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { Button } from '@/react/components/UI/Button';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { formatDisplayDateTime, isInPeriod } from '../../utils/dossierUtils';

const CONSULTATION_KEYWORDS = ['consultation', 'suivi', 'contrôle', 'visite'];

function isConsultation(reason?: string): boolean {
    if (!reason) return true;
    const lower = reason.toLowerCase();
    return CONSULTATION_KEYWORDS.some((kw) => lower.includes(kw)) || true;
}

export function ConsultationsTab() {
    const { data, period, selectedDate, isReadOnly, openAppointmentModal, openNoteModal } = usePatientDossierContext();

    const consultations = data.appointments
        .filter((appt) => isConsultation(appt.reason))
        .filter((appt) => isInPeriod(appt.scheduledAt, period, selectedDate))
        .sort((a, b) => new Date(b.scheduledAt).getTime() - new Date(a.scheduledAt).getTime());

    const relatedNotes = data.notes.filter((note) => {
        const date = note.notedAt ?? note.createdAt;
        return isInPeriod(date, period, selectedDate);
    });

    return (
            <div className="patient-dossier-tab patient-dossier-tab--consultations">
                <div className="patient-dossier-tab__toolbar">
                    <p className="patient-dossier-tab__hint">Consultations et comptes-rendus cliniques.</p>
                    {!isReadOnly && (
                        <div className="consultations-tab__actions">
                            <Button variant="secondary" onClick={() => openAppointmentModal()}>
                                + Planifier consultation
                            </Button>
                            <Button variant="primary" onClick={openNoteModal}>
                                + Note de consultation
                            </Button>
                        </div>
                    )}
                </div>

            <div className="patient-dossier-tab__section">
                <h3>Consultations</h3>
                {consultations.length === 0 ? (
                    <Card><p>Aucune consultation sur la période.</p></Card>
                ) : (
                    <div className="patient-dossier-tab__grid">
                        {consultations.map((appt) => (
                            <Card key={appt.id}>
                                <div className="patient-dossier-tab__card-header">
                                    <h4>{formatDisplayDateTime(appt.scheduledAt)}</h4>
                                    <Badge variant={appt.status === 'COMPLETED' ? 'success' : 'info'}>{appt.status}</Badge>
                                </div>
                                <p><strong>Motif :</strong> {appt.reason || 'Consultation'}</p>
                                {appt.durationMinutes != null && <p><strong>Durée :</strong> {appt.durationMinutes} min</p>}
                            </Card>
                        ))}
                    </div>
                )}
            </div>

            <div className="patient-dossier-tab__section">
                <h3>Notes associées</h3>
                {relatedNotes.length === 0 ? (
                    <Card><p>Aucune note sur la période.</p></Card>
                ) : (
                    <div className="patient-dossier-tab__notes">
                        {relatedNotes.map((note) => (
                            <Card key={note.id}>
                                <p className="patient-dossier-tab__note-meta">
                                    {formatDisplayDateTime(note.notedAt ?? note.createdAt)}
                                </p>
                                <p>{note.content}</p>
                            </Card>
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}
