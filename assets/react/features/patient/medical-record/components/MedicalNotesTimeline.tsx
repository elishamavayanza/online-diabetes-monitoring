import { useMemo } from 'react';
import { MedicalNoteInfo } from '../types';

interface MedicalNotesTimelineProps {
    notes: MedicalNoteInfo[];
    selectedDate?: Date | null;
    onClearFilter?: () => void;
}

function toDateKey(date: Date): string {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function formatGroupHeader(iso: string): string {
    return new Date(iso).toLocaleDateString('fr-FR', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
}

function formatTime(iso: string): string {
    const date = new Date(iso);
    if (isNaN(date.getTime())) return '';
    return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
}

export function MedicalNotesTimeline({ notes, selectedDate, onClearFilter }: MedicalNotesTimelineProps) {
    const filtered = useMemo(() => {
        if (!selectedDate) return notes;
        const key = toDateKey(selectedDate);
        return notes.filter((n) => toDateKey(new Date(n.date)) === key);
    }, [notes, selectedDate]);

    const groups = useMemo(() => {
        const map = new Map<string, MedicalNoteInfo[]>();
        filtered.forEach((note) => {
            const key = toDateKey(new Date(note.date));
            const list = map.get(key) ?? [];
            list.push(note);
            map.set(key, list);
        });
        return Array.from(map.entries())
            .sort((a, b) => (a[0] < b[0] ? 1 : -1))
            .map(([key, items]) => ({
                key,
                date: items[0].date,
                items: items.sort(
                    (a, b) => new Date(a.date).getTime() - new Date(b.date).getTime(),
                ),
            }));
    }, [filtered]);

    return (
        <div className="medical-notes-timeline">
            <div className="medical-notes-timeline__header">
                <h2>Notes médicales</h2>
                {selectedDate && (
                    <button
                        type="button"
                        className="medical-notes-timeline__clear"
                        onClick={onClearFilter}
                    >
                        Afficher toutes les dates
                    </button>
                )}
            </div>

            {filtered.length === 0 ? (
                <p className="medical-notes-timeline__empty">
                    {selectedDate
                        ? 'Aucune note médicale enregistrée à cette date.'
                        : 'Aucune note médicale pour le moment.'}
                </p>
            ) : (
                <ol className="medical-notes-timeline__list">
                    {groups.map((group) => (
                        <li key={group.key} className="medical-notes-timeline__day">
                            <div className="medical-notes-timeline__day-header">
                                <span className="medical-notes-timeline__dot" />
                                <time>{formatGroupHeader(group.date)}</time>
                            </div>
                            <div className="medical-notes-timeline__items">
                                {group.items.map((note) => (
                                    <article key={note.id} className="medical-notes-timeline__note">
                                        <div className="medical-notes-timeline__note-meta">
                                            <span className="medical-notes-timeline__note-time">
                                                {formatTime(note.date)}
                                            </span>
                                            {note.authorName && (
                                                <span className="medical-notes-timeline__note-author">
                                                    Par {note.authorName}
                                                </span>
                                            )}
                                        </div>
                                        <p className="medical-notes-timeline__note-content">{note.content}</p>
                                    </article>
                                ))}
                            </div>
                        </li>
                    ))}
                </ol>
            )}
        </div>
    );
}