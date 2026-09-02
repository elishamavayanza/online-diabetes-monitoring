export interface AgendaAppointment {
    id: string;
    time: string;
    patient: string;
    motif: string;
    type?: 'Consultation' | 'Suivi diabète';
}

export interface AgendaDay {
    date: string;
    label: string;
    appointments: AgendaAppointment[];
}

export interface AgendaData {
    days: AgendaDay[];
}
