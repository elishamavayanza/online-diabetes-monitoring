import { Badge } from '@/react/components/UI/Badge/Badge';
import { AgendaRecord, statusToBadgeVariant, statusLabel } from '../types';
import { useI18n } from '@/react/i18n/I18nContext';
import { formatDate } from '@/react/i18n/formatters';

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

export function HistoryDayPanel({ date, records, onClear }: HistoryDayPanelProps) {
    const { locale, t } = useI18n();
    const key = toDateKey(date);
    const dayRecords = records
        .filter((record) => record.date === key)
        .sort((a, b) => a.time.localeCompare(b.time));

    return (
        <section className="agenda-history-day">
            <div className="agenda-history-day__header">
                <h2>{formatDate(date, locale, { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' })}</h2>
                <button
                    type="button"
                    className="agenda-history-day__clear"
                    onClick={onClear}
                >
                    {t('Effacer la sélection')}
                </button>
            </div>

            {dayRecords.length === 0 ? (
                <p className="agenda-history-day__empty">
                    {t('Aucun rendez-vous à cette date. Sélectionnez une date marquée dans le calendrier.')}
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
                                    <Badge variant="info">{t('Passé')}</Badge>
                                ) : (
                                    <Badge variant="success">{t('À venir')}</Badge>
                                )}
                                {record.status && (
                                    <Badge variant={statusToBadgeVariant(record.status)}>
                                        {t(statusLabel(record.status))}
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
