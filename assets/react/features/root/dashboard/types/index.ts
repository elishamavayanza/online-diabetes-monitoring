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

export interface PlatformStatusItem {
    id: string;
    label: string;
    isActive: boolean;
}

export interface DashboardData {
    stats: StatCardData[];
    recentActivities: RecentActivity[];
    platformStatus: PlatformStatusItem[];
}
