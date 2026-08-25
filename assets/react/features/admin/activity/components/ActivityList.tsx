import { Card } from '@/react/components/UI/Card';
import { Badge } from '@/react/components/UI/Badge';
import { ActivityItem } from '../types';

interface ActivityListProps {
    activities: ActivityItem[];
}

const variantMap = {
    info: 'primary',
    success: 'success',
    warning: 'warning',
} as const;

export function ActivityList({ activities }: ActivityListProps) {
    return (
        <Card className="activity-card">
            <ul className="activity-list">
                {activities.map((activity) => (
                    <li key={activity.id} className="activity-item">
                        <Badge variant={variantMap[activity.type]}>{activity.type}</Badge>
                        <span className="activity-message">{activity.message}</span>
                        <span className="activity-timestamp">{activity.timestamp}</span>
                    </li>
                ))}
            </ul>
        </Card>
    );
}
