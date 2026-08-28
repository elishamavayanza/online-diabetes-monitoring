// hooks/useCreateAppointment.ts
import { useState } from 'react';
import { createAppointment } from '../services/appointmentService';
import {AppointmentFormData, Appointment} from "@/react/features/admin/appointments/types/types";

interface UseCreateAppointmentReturn {
    createAppointment: (data: AppointmentFormData) => Promise<Appointment | null>;
    isLoading: boolean;
    error: string | null;
}

export function useCreateAppointment(): UseCreateAppointmentReturn {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleCreate = async (data: AppointmentFormData): Promise<Appointment | null> => {
        setIsLoading(true);
        setError(null);
        try {
            const newAppointment = await createAppointment(data);
            return newAppointment;
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Une erreur est survenue');
            return null;
        } finally {
            setIsLoading(false);
        }
    };

    return {
        createAppointment: handleCreate,
        isLoading,
        error,
    };
}
