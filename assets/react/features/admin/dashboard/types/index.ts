export interface StatCardData {
    id: string;
    label: string;
    value: number;
    icon?: React.ReactNode;
}

export interface RecentActivity {
    id: string;
    message: string;
    timestamp: string;
}

export interface AppointmentToday {
    id: string;
    time: string;
    doctor: string;
    patient: string;
}

export interface OrganizationStatusItem {
    id: string;
    label: string;
    isActive: boolean;
}

export interface AdminDashboardData {
    stats: StatCardData[];
    recentActivities: RecentActivity[];
    appointmentsToday: AppointmentToday[];
    organizationStatus: OrganizationStatusItem[];
}
