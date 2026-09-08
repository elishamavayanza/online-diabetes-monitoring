export interface AgendaAppointment {
    id: string;
    time: string;
    patient: string;
    patientId: string;
    motif: string;
    type: 'Consultation' | 'Suivi diabète';
    status?: string;
    durationMinutes?: number;
    isPast?: boolean;
}

export interface AgendaDay {
    date: string;
    label: string;
    appointments: AgendaAppointment[];
}

export interface AgendaRecord {
    id: string;
    date: string;
    dateTime: string;
    time: string;
    patient: string;
    motif: string;
    status?: string;
    isPast: boolean;
}

export interface AgendaStat {
    id: string;
    label: string;
    value: number;
}

export interface AgendaData {
    days: AgendaDay[];
    records: AgendaRecord[];
    stats: AgendaStat[];
}

export const STATUS_LABEL: Record<string, string> = {
    SCHEDULED: 'Planifié',
    CONFIRMED: 'Confirmé',
    CANCELLED: 'Annulé',
    COMPLETED: 'Terminé',
    NO_SHOW: 'Absent',
    RESCHEDULED: 'Reporté',
};

export function statusToBadgeVariant(status?: string): 'default' | 'info' | 'success' | 'warning' | 'error' {
    switch ((status ?? '').toUpperCase()) {
        case 'CONFIRMED':
            return 'success';
        case 'COMPLETED':
            return 'info';
        case 'CANCELLED':
        case 'NO_SHOW':
            return 'error';
        case 'SCHEDULED':
        case 'RESCHEDULED':
            return 'warning';
        default:
            return 'default';
    }
}

export function statusLabel(status?: string): string {
    const normalized = (status ?? '').toUpperCase();
    return STATUS_LABEL[normalized] ?? status ?? '—';
}