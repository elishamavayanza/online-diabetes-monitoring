import { useEffect, useState } from 'react';
import { fetchAppointments } from '../services/appointmentsService';
import { Appointment, AppointmentPeriod } from '../types';

export function useAppointments() {
    const [appointments, setAppointments] = useState<Appointment[]>([]);
    const [period, setPeriod] = useState<AppointmentPeriod>('today');
    const [viewMode, setViewMode] = useState<'list' | 'calendar'>('list');
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const data = await fetchAppointments(period);
                setAppointments(data);
            } catch (err) {
                setError('Impossible de charger les rendez-vous.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, [period]);

    return { appointments, period, setPeriod, viewMode, setViewMode, isLoading, error };
}
