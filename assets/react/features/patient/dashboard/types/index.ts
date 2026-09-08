export interface HealthMetric {
    id: string;
    label: string;
    value: string;
    unit: string;
    date?: string;
    tone?: 'neutral' | 'good' | 'warning' | 'critical';
}

export interface NextAppointment {
    date: string;
    time: string;
    doctor: string;
}

export interface NextMedication {
    time: string;
    name: string;
}

export type WatchLevel = 'info' | 'warning' | 'critical';

export interface WatchItem {
    id: string;
    message: string;
    level?: WatchLevel;
}

export interface UpcomingAppointment {
    id: string;
    date: string;
    time: string;
    doctor: string;
    reason?: string;
    status: string;
}

export interface ActiveTreatment {
    id: string;
    name: string;
    dosage?: string;
    morning: boolean;
    noon: boolean;
    evening: boolean;
    instructions?: string;
}

export interface RecentNote {
    id: string;
    content: string;
    authorName?: string;
    date: string;
}

export interface PatientDashboardData {
    patientName: string;
    metrics: HealthMetric[];
    nextAppointment: NextAppointment;
    nextMedication: NextMedication;
    watchList: WatchItem[];
    upcomingAppointments: UpcomingAppointment[];
    treatments: ActiveTreatment[];
    recentNotes: RecentNote[];
    hasRecord: boolean;
}