export interface NutritionistStat {
    id: string;
    label: string;
    value: number;
}

export interface AppointmentToday {
    id: string;
    time: string;
    patient: string;
}

export interface UpcomingAppointment {
    id: string;
    date: string;
    time: string;
    patient: string;
    reason?: string;
    isToday?: boolean;
}

export interface FollowUpPatient {
    id: string;
    name: string;
    lastVisit: string;
}

export interface RecentActivity {
    id: string;
    message: string;
    timestamp: string;
}

export interface NutritionistDashboardData {
    stats: NutritionistStat[];
    appointmentsToday: AppointmentToday[];
    upcomingAppointments: UpcomingAppointment[];
    followUpPatients: FollowUpPatient[];
    recentActivities: RecentActivity[];
}