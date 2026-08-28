// src/react/features/admin/appointments/types.ts
export type AppointmentStatus = 'Pending' | 'Confirmed' | 'Cancelled';

export interface Appointment {
    id: string;
    patient: string;
    professionnel: string;
    etablissement: string;
    date: string; // 'YYYY-MM-DD'
    heure: string;
    statut: AppointmentStatus;
}

export type AppointmentPeriod = 'today' | 'week' | 'month' | 'history' | 'all';
export interface AppointmentFormData {
    patientId: string;
    professionalId: string;
    organizationId: string;
    facilityId?: string | null;
    scheduledAt: string; // format datetime-local ou ISO
    durationMinutes: number;
    status: string;
    reason?: string;
    notes?: string;
}

export interface SelectOption {
    value: string;
    label: string;
}
