export interface ClinicianStat {
    id: string;
    label: string;
    value: number;
}

export interface AppointmentToday {
    id: string;
    time: string;
    patient: string;
}

export interface RecentActivity {
    id: string;
    message: string;
    timestamp: string;
}

export interface ClinicianDashboardData {
    stats: ClinicianStat[];
    appointmentsToday: AppointmentToday[];
    recentActivities: RecentActivity[];
}
