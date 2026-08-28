// hooks/useUpdateAppointment.ts
import { useState } from 'react';
import { AppointmentFormData, Appointment } from '../types';
import { updateAppointment } from '../services/appointmentService';

interface UseUpdateAppointmentReturn {
    updateAppointment: (id: string, data: AppointmentFormData) => Promise<Appointment | null>;
    isUpdating: boolean;
    error: string | null;
}

export function useUpdateAppointment(): UseUpdateAppointmentReturn {
    const [isUpdating, setIsUpdating] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleUpdate = async (id: string, data: AppointmentFormData): Promise<Appointment | null> => {
        setIsUpdating(true);
        setError(null);
        try {
            const updated = await updateAppointment(id, data);
            return updated;
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Erreur lors de la mise à jour');
            return null;
        } finally {
            setIsUpdating(false);
        }
    };

    return {
        updateAppointment: handleUpdate,
        isUpdating,
        error,
    };
}
