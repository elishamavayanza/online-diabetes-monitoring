import { Badge } from '@/react/components/UI/Badge/Badge';
import { AgendaRecord, statusToBadgeVariant, statusLabel } from '../types';

interface HistoryDayPanelProps {
    date: Date;
    records: AgendaRecord[];
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

export function HistoryDayPanel({ date, records, onClear }: HistoryDayPanelProps) {
    const key = toDateKey(date);
    const dayRecords = records
        .filter((record) => record.date === key)
        .sort((a, b) => a.time.localeCompare(b.time));

    return (
        <section className="agenda-history-day">
            <div className="agenda-history-day__header">
                <h2>{formatDate(date)}</h2>
                <button
                    type="button"
                    className="agenda-history-day__clear"
                    onClick={onClear}
                >
                    Effacer la sélection
                </button>
            </div>

            {dayRecords.length === 0 ? (
                <p className="agenda-history-day__empty">
                    Aucun rendez-vous à cette date. Sélectionnez une date marquée dans le calendrier.
                </p>
            ) : (
                <ul className="agenda-history-day__list">
                    {dayRecords.map((record) => (
                        <li
                            key={record.id}
                            className={`agenda-history-item ${record.isPast ? 'agenda-history-item--past' : 'agenda-history-item--upcoming'}`}
                        >
                            <span className="agenda-history-item__time">{record.time}</span>
                            <div className="agenda-history-item__info">
                                <span className="agenda-history-item__patient">{record.patient}</span>
                                <span className="agenda-history-item__motif">{record.motif}</span>
                            </div>
                            <div className="agenda-history-item__badges">
                                {record.isPast ? (
                                    <Badge variant="info">Passé</Badge>
                                ) : (
                                    <Badge variant="success">À venir</Badge>
                                )}
                                {record.status && (
                                    <Badge variant={statusToBadgeVariant(record.status)}>
                                        {statusLabel(record.status)}
                                    </Badge>
                                )}
                            </div>
                        </li>
                    ))}
                </ul>
            )}
        </section>
    );
}