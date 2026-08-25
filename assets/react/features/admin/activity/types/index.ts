export interface ActivityItem {
    id: string;
    message: string;
    timestamp: string;
    type: 'info' | 'success' | 'warning';
}
