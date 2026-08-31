import { Card } from '@/react/components/UI/Card';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { formatDisplayDateTime } from '../../utils/dossierUtils';

export function NotesTab() {
    const { data } = usePatientDossierContext();
    const { notes } = data;

    return (
        <div className="patient-dossier-tab patient-dossier-tab--notes">
            {notes.length === 0 ? (
                <Card><p>Aucune note médicale enregistrée.</p></Card>
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
