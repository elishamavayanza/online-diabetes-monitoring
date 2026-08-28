import { useEffect, useState } from 'react';
import { fetchAppointments } from '../services/appointmentsService'; // corrigez le chemin si nécessaire
import { Appointment, AppointmentPeriod } from '../types/types';

export function useAppointments() {
    const [allAppointments, setAllAppointments] = useState<Appointment[]>([]);
    const [appointments, setAppointments] = useState<Appointment[]>([]);
    const [period, setPeriod] = useState<AppointmentPeriod>('today');
    const [viewMode, setViewMode] = useState<'list' | 'calendar'>('list');
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    // Charger tous les rendez-vous une seule fois
    useEffect(() => {
        const loadAll = async () => {
            setIsLoading(true);
            setError(null);
            try {
                const data = await fetchAppointments('all');
                setAllAppointments(data);
            } catch (err) {
                setError('Impossible de charger les rendez-vous.');
            } finally {
                setIsLoading(false);
            }
        };
        loadAll();
    }, []);

    // Filtrer localement selon la période
    useEffect(() => {
        const now = new Date();
        let filtered: Appointment[] = allAppointments;

        switch (period) {
            case 'history':
                filtered = allAppointments.filter((appt) => {
                    const [year, month, day] = appt.date.split('-').map(Number);
                    const [hour, minute] = appt.heure.split(':').map(Number);
                    return new Date(year, month - 1, day, hour, minute) < now;
                });
                break;
            case 'today':
                filtered = allAppointments.filter((appt) => {
                    const todayKey = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
                    return appt.date === todayKey && !isPast(appt, now);
                });
                break;
            case 'week':
                filtered = allAppointments.filter((appt) => {
                    const apptDate = new Date(appt.date);
                    const diffDays = Math.floor((apptDate.getTime() - now.getTime()) / (1000 * 60 * 60 * 24));
                    return diffDays >= 0 && diffDays < 7 && !isPast(appt, now);
                });
                break;
            case 'month':
                filtered = allAppointments.filter((appt) => {
                    const apptDate = new Date(appt.date);
                    return apptDate.getMonth() === now.getMonth() && apptDate.getFullYear() === now.getFullYear() && !isPast(appt, now);
                });
                break;
            default:
                filtered = allAppointments;
        }

        setAppointments(filtered);
    }, [allAppointments, period]);

    return { allAppointments, appointments, period, setPeriod, viewMode, setViewMode, isLoading, error };
}

// Fonction utilitaire locale (évite de dépendre du service)
function isPast(appt: Appointment, now: Date): boolean {
    const [year, month, day] = appt.date.split('-').map(Number);
    const [hour, minute] = appt.heure.split(':').map(Number);
    return new Date(year, month - 1, day, hour, minute) < now;
}
