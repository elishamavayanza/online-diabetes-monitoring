import React from 'react';
import { Calendar } from '@/react/components/Calendars/Calendar';
import { Appointment } from '../types/types';

interface AppointmentsCalendarProps {
    appointments: Appointment[];
    selectedDate?: Date | null;
    onDateSelect?: (date: Date) => void;
}

export function AppointmentsCalendar({
                                         appointments,
                                         selectedDate,
                                         onDateSelect,
                                     }: AppointmentsCalendarProps) {
    // Transformer les rendez-vous en liste de marqueurs (CalendarMarkedDate[])
    const markedDates = React.useMemo(() => {
        // Regrouper les marqueurs par date
        const dateMap = new Map<string, string[]>();
        appointments.forEach((appt) => {
            const dateKey = appt.date; // ex: 'YYYY-MM-DD'
            if (!dateMap.has(dateKey)) {
                dateMap.set(dateKey, ['has-appointment']);
            }
        });

        // Convertir en tableau d'objets CalendarMarkedDate
        return Array.from(dateMap.entries()).map(([date, markers]) => ({
            date: new Date(date),   // ou garder `date` si le type accepte string
            markers,
        }));
    }, [appointments]);

    return (
        <Calendar
            selectedDate={selectedDate}
            onDateSelect={onDateSelect}
            markedDates={markedDates}
        />
    );
}
