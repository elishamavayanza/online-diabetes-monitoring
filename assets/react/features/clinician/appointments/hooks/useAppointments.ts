import { useEffect, useState, useCallback } from 'react';
import { fetchAppointments } from '../services/appointmentsService';
import { Appointment, AppointmentFilter } from '../types';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function useAppointments() {
    const { showToast } = useToast();
    const [appointments, setAppointments] = useState<Appointment[]>([]);
    const [filter, setFilter] = useState<AppointmentFilter>('today');
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchAppointments(filter);
            setAppointments(data);
        } catch (err) {
            const message = 'Impossible de charger les rendez-vous.';
            setError(message);
            showToast({
                type: 'error',
                message,
            });
        } finally {
            setIsLoading(false);
        }
    }, [filter, showToast]);

    useEffect(() => {
        load();
    }, [load]);

    return { appointments, filter, setFilter, isLoading, error, reload: load };
}
