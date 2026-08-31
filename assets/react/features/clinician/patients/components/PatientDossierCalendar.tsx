// PatientDossierCalendar.tsx
import { useMemo } from 'react';
import { Calendar } from '@/react/components/Calendars/Calendar';
import { DossierTabId, PatientDossierData } from '../types';
import { collectMarkedDatesForTab } from '../utils/dossierUtils';

const TAB_CALENDAR_LABELS: Partial<Record<DossierTabId, string>> = {
    measurements: 'Dates avec des mesures',
    prescriptions: 'Dates de prescriptions',
    consultations: 'Dates de consultations',
    nutrition: 'Dates de repas',
    appointments: 'Dates de rendez-vous',
    notes: 'Dates de notes',
    'medical-profile': 'Dates du profil médical',
};

interface PatientDossierCalendarProps {
    data: PatientDossierData;
    activeTab: DossierTabId;
    selectedDate?: Date | null;
    onDateSelect?: (date: Date) => void;
}

export function PatientDossierCalendar({ data, activeTab, selectedDate, onDateSelect }: PatientDossierCalendarProps) {
    const markedDates = useMemo(
        () => collectMarkedDatesForTab(activeTab, data),
        [activeTab, data],
    );

    const hint = TAB_CALENDAR_LABELS[activeTab];

    return (
        <div className="patient-dossier-calendar">
            {hint && <p className="patient-dossier-calendar__hint">{hint}</p>}
            <Calendar
                selectedDate={selectedDate}
                onDateSelect={onDateSelect}
                markedDates={markedDates}
            />
        </div>
    );
}
