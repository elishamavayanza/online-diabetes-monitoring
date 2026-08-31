import React, { useMemo } from 'react';
import { Calendar } from '@/react/components/Calendars/Calendar';
import { PatientDossierData } from '../types';
import { collectMarkedDates } from '../utils/dossierUtils';

interface PatientDossierCalendarProps {
    data: PatientDossierData;
    selectedDate?: Date | null;
    onDateSelect?: (date: Date) => void;
}

export function PatientDossierCalendar({ data, selectedDate, onDateSelect }: PatientDossierCalendarProps) {
    const markedDates = useMemo(() => {
        const allMeasurements = [
            ...data.measurements.bloodGlucose,
            ...data.measurements.bloodPressure,
            ...data.measurements.hba1c,
            ...data.measurements.weight,
            ...data.measurements.physicalActivity,
        ];
        return collectMarkedDates(data.appointments, allMeasurements);
    }, [data]);

    return (
        <Calendar
            selectedDate={selectedDate}
            onDateSelect={onDateSelect}
            markedDates={markedDates}
        />
    );
}
