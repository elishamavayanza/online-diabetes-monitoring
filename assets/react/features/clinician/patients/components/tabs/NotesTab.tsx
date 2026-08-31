import { Card } from '@/react/components/UI/Card';
import { Button } from '@/react/components/UI/Button';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { formatDisplayDateTime, isInPeriod } from '../../utils/dossierUtils';

export function NotesTab() {
    const { data, period, selectedDate, isReadOnly, openNoteModal } = usePatientDossierContext();
    const notes = data.notes
        .filter((note) => isInPeriod(note.notedAt ?? note.createdAt, period, selectedDate))
        .sort((a, b) => new Date(b.notedAt ?? b.createdAt).getTime() - new Date(a.notedAt ?? a.createdAt).getTime());

    return (
        <div className="patient-dossier-tab patient-dossier-tab--notes">
            <div className="patient-dossier-tab__toolbar">
                <p className="patient-dossier-tab__hint">Notes et observations cliniques.</p>
                {!isReadOnly && (
                    <Button variant="primary" onClick={openNoteModal}>
                        + Nouvelle note
                    </Button>
                )}
            </div>

            {notes.length === 0 ? (
                <Card><p>Aucune note sur la période sélectionnée.</p></Card>
            ) : (
                <div className="patient-dossier-tab__notes">
                    {notes.map((note) => (
                        <Card key={note.id}>
                            <p className="patient-dossier-tab__note-meta">
                                {formatDisplayDateTime(note.notedAt ?? note.createdAt)}
                                {note.authorName && ` — ${note.authorName}`}
                            </p>
                            <p>{note.content}</p>
                        </Card>
                    ))}
                </div>
            )}
        </div>
    );
}
