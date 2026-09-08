import { useMemo, useState } from 'react';
import { useAgenda } from '../hooks/useAgenda';
import { AgendaDayCard } from '../components/AgendaDayCard';
import { HistoryDayPanel } from '../components/HistoryDayPanel';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { RightSidebar } from '@/react/components/Navigation/RightSidebar';
import { Calendar } from '@/react/components/Calendars/Calendar';
import type { CalendarMarkedDate } from '@/react/hook-components/Calendars/Calendar';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/clinician/agenda/_agenda.scss';

function toDateKey(date: Date): string {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

const LEGEND: { label: string; type: CalendarMarkedDate['type'] }[] = [
    { label: 'Passés', type: 'info' },
    { label: 'À venir', type: 'success' },
];

export function AgendaPage() {
    const { data, isLoading, error } = useAgenda();
    const [selectedDate, setSelectedDate] = useState<Date | null>(null);
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const markedDates = useMemo<CalendarMarkedDate[]>(() => {
        if (!data) return [];
        const seen = new Set<string>();
        const result: CalendarMarkedDate[] = [];
        data.records.forEach((record) => {
            if (seen.has(record.date)) return;
            seen.add(record.date);
            result.push({
                date: new Date(`${record.date}T12:00:00`),
                type: record.isPast ? 'info' : 'success',
            });
        });
        return result;
    }, [data]);

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    const handleDateSelect = (date: Date) => {
        const previous = selectedDate;
        setSelectedDate(date);
        pushAction(() => setSelectedDate(previous));
    };

    const clearDate = () => {
        const previous = selectedDate;
        setSelectedDate(null);
        pushAction(() => setSelectedDate(previous));
    };

    if (isLoading) return <Spinner />;
    if (error || !data) return <Alert variant="error">{error ?? 'Aucune donnée'}</Alert>;

    return (
        <div className="agenda-page">
            <div className="agenda-page__header">
                <h1>Agenda</h1>
                <div className="agenda-page__header-info">
                    <p>Votre planning de la semaine</p>
                    <div className="agenda-page__stats">
                        {data.stats.map((stat) => (
                            <span key={stat.id} className="agenda-page__stat">
                                <strong>{stat.value}</strong> {stat.label}
                            </span>
                        ))}
                    </div>
                </div>
                <Button variant="secondary" onClick={openHelp}>Aide</Button>
            </div>

            <div className="agenda-page__body">
                <div className="agenda-page__content">
                    <div className="agenda-page__days">
                        {data.days.map((day) => (
                            <AgendaDayCard key={day.date} day={day} />
                        ))}
                    </div>
                </div>

                <RightSidebar
                    collapsible
                    size="medium"
                    minWidth={260}
                    maxWidth={420}
                    closeThreshold={80}
                    collapsedWidth={35}
                    title="Historique"
                    header={<div>Naviguez par date</div>}
                >
                    <div className="agenda-page__right-content">
                        <Calendar
                            selectedDate={selectedDate}
                            onDateSelect={handleDateSelect}
                            markedDates={markedDates}
                        />

                        <ul className="agenda-page__legend">
                            {LEGEND.map((item) => (
                                <li key={item.label}>
                                    <span className={`agenda-page__legend-dot agenda-page__legend-dot--${item.type}`} />
                                    {item.label}
                                </li>
                            ))}
                        </ul>

                        {selectedDate && (
                            <HistoryDayPanel
                                date={selectedDate}
                                records={data.records}
                                onClear={clearDate}
                            />
                        )}
                    </div>
                </RightSidebar>
            </div>

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>Votre agenda hebdomadaire. Utilisez le calendrier à droite pour consulter l'historique de vos rendez-vous par date.</p>
                </Modal>
            )}
        </div>
    );
}