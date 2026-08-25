import { useEffect, useState } from 'react';
import { fetchAppointments } from '../services/appointmentsService';
import { Appointment, AppointmentFilter } from '../types';

export function useAppointments() {
    const [appointments, setAppointments] = useState<Appointment[]>([]);
    const [filter, setFilter] = useState<AppointmentFilter>('today');
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const data = await fetchAppointments(filter);
                setAppointments(data);
            } catch (err) {
                setError('Impossible de charger les rendez-vous.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, [filter]);

    return { appointments, filter, setFilter, isLoading, error };
}
