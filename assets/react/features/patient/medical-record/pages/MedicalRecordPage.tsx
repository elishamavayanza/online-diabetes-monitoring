import { useState, useMemo } from 'react';
import { useMedicalRecord } from '../hooks/useMedicalRecord';
import { MedicalRecordSections } from '../components/MedicalRecordSections';
import { MedicalNotesTimeline } from '../components/MedicalNotesTimeline';
import { DaySnapshotPanel } from '../components/DaySnapshotPanel';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { RightSidebar } from '@/react/components/Navigation/RightSidebar';
import { Calendar } from '@/react/components/Calendars/Calendar';
import type { CalendarMarkedDate } from '@/react/hook-components/Calendars/Calendar';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { useI18n } from '@/react/i18n/I18nContext';
import { RecordEventKind } from '../types';
import '@/styles/pages/patient/medical-record/_record.scss';

function toDateKey(date: Date): string {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

const EVENT_MARKER_TYPE: Record<RecordEventKind, CalendarMarkedDate['type']> = {
    note: 'info',
    appointment: 'success',
    measurement: 'warning',
};

const LEGEND: { label: string; type: CalendarMarkedDate['type'] }[] = [
    { label: 'Notes', type: 'info' },
    { label: 'Rendez-vous', type: 'success' },
    { label: 'Mesures', type: 'warning' },
];

export function MedicalRecordPage() {
    const { t } = useI18n();
    const { data, isLoading, error } = useMedicalRecord();
    const [selectedDate, setSelectedDate] = useState<Date | null>(null);
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const markedDates = useMemo<CalendarMarkedDate[]>(() => {
        if (!data) return [];
        const seen = new Set<string>();
        const result: CalendarMarkedDate[] = [];
        data.events.forEach((event) => {
            const key = `${toDateKey(new Date(event.date))}-${event.kind}`;
            if (seen.has(key)) return;
            seen.add(key);
            result.push({
                date: new Date(`${toDateKey(new Date(event.date))}T12:00:00`),
                type: EVENT_MARKER_TYPE[event.kind],
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
    if (error || !data) return <Alert variant="error">{error ?? t('Aucune donnée')}</Alert>;

    return (
        <div className="medical-record-page">
            <div className="medical-record-page__header">
                <h1>{t('Mon dossier')}</h1>
                <p>{t('Votre carnet de santé : vos informations médicales et notes par date')}</p>
                <Button variant="secondary" onClick={openHelp}>{t('Aide')}</Button>
            </div>

            <div className="medical-record-page__body">
                <div className="medical-record-page__content">
                    {selectedDate && (
                        <DaySnapshotPanel date={selectedDate} events={data.events} onClear={clearDate} />
                    )}

                    <MedicalRecordSections data={data} />

                    <MedicalNotesTimeline
                        notes={data.notes}
                        selectedDate={selectedDate}
                        onClearFilter={clearDate}
                    />
                </div>

                <RightSidebar
                    collapsible
                    size="medium"
                    minWidth={250}
                    maxWidth={400}
                    closeThreshold={80}
                    collapsedWidth={35}
                    title={t('Carnet')}
                    header={<div>{t('Naviguez par date')}</div>}
                >
                    <div className="medical-record-page__right-content">
                        <Calendar
                            selectedDate={selectedDate}
                            onDateSelect={handleDateSelect}
                            markedDates={markedDates}
                        />

                        <ul className="medical-record-page__legend">
                            {LEGEND.map((item) => (
                                <li key={item.label}>
                                    <span className={`medical-record-page__legend-dot medical-record-page__legend-dot--${item.type}`} />
                                    {t(item.label)}
                                </li>
                            ))}
                        </ul>

                            {selectedDate && (
                            <Button variant="secondary" size="small" onClick={clearDate}>
                                {t('Effacer la sélection')}
                            </Button>
                        )}
                    </div>
                </RightSidebar>
            </div>

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>{t('Sélectionnez une date dans le calendrier pour afficher les événements de la journée (notes médicales, rendez-vous, mesures). C\u2019est votre carnet de santé.')}</p>
                </Modal>
            )}
        </div>
    );
}