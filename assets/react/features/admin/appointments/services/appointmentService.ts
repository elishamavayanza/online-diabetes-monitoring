// services/appointmentService.ts

import {AppointmentFormData} from "@/react/features/admin/appointments/types/types";
import {Appointment} from "@/react/features/admin/appointments/types/types";

const API_BASE_URL = '/api'; // ou votre base URL

export async function createAppointment(payload: AppointmentFormData): Promise<Appointment> {
    const response = await fetch(`${API_BASE_URL}/appointments`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload),
    });

    if (!response.ok) {
        const error = await response.json().catch(() => ({}));
        throw new Error(error.message || 'Erreur lors de la création du rendez-vous');
    }

    return response.json();
}
// services/appointmentService.ts (extrait)
export async function updateAppointment(
    id: string,
    payload: AppointmentFormData
): Promise<Appointment> {
    const response = await fetch(`${API_BASE_URL}/appointments/${id}`, {
        method: 'PUT', // ou 'PATCH'
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
    });

    if (!response.ok) {
        const error = await response.json().catch(() => ({}));
        throw new Error(error.message || 'Erreur lors de la mise à jour du rendez-vous');
    }

    return response.json();
}
