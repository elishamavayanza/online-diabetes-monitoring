import { Badge } from '@/react/components/UI/Badge';
import { RecordEvent, RecordEventKind } from '../types';

interface DaySnapshotPanelProps {
    date: Date;
    events: RecordEvent[];
    onClear: () => void;
}

function toDateKey(date: Date): string {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function formatDate(date: Date): string {
    return date.toLocaleDateString('fr-FR', {
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

const KIND_LABEL: Record<RecordEventKind, string> = {
    note: 'Notes',
    appointment: 'Rendez-vous',
    measurement: 'Mesures',
};

export function DaySnapshotPanel({ date, events, onClear }: DaySnapshotPanelProps) {
    const key = toDateKey(date);
    const dayEvents = events.filter((e) => toDateKey(new Date(e.date)) === key);

    const byKind = (kind: RecordEventKind) => dayEvents.filter((e) => e.kind === kind);

    return (
        <section className="day-snapshot">
            <div className="day-snapshot__header">
                <h2>{formatDate(date)}</h2>
                <button type="button" className="day-snapshot__clear" onClick={onClear}>
                    Effacer la sélection
                </button>
            </div>

            {dayEvents.length === 0 ? (
                <p className="day-snapshot__empty">
                    Aucun événement enregistré à cette date.
                    Sélectionnez une des dates marquées dans le calendrier.
                </p>
            ) : (
                <div className="day-snapshot__body">
                    {(['note', 'appointment', 'measurement'] as RecordEventKind[]).map((kind) => {
                        const items = byKind(kind);
                        if (items.length === 0) return null;
                        return (
                            <div key={kind} className="day-snapshot__group">
                                <h3>
                                    {KIND_LABEL[kind]}
                                    <span className="day-snapshot__count">{items.length}</span>
                                </h3>
                                <ul>
                                    {items.map((item) => (
                                        <li key={item.id} className={`day-snapshot__item day-snapshot__item--${kind}`}>
                                            <span className="day-snapshot__time">{formatTime(item.date)}</span>
                                            <span className="day-snapshot__label">{item.label}</span>
                                            {item.meta && <span className="day-snapshot__meta">{item.meta}</span>}
                                            {item.kind === 'appointment' && item.status && (
                                                <Badge variant="info">{item.status}</Badge>
                                            )}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        );
                    })}
                </div>
            )}
        </section>
    );
}