import { Card } from '@/react/components/UI/Card';
import { RecentActivity } from '../types';

interface RecentActivityListProps {
    activities: RecentActivity[];
}

export function RecentActivityList({ activities }: RecentActivityListProps) {
    return (
        <Card className="recent-activity">
            <h2 className="section-title">Activité récente</h2>
            <ul>
                {activities.map((activity) => (
                    <li key={activity.id} className="recent-activity__item">
                        <span>{activity.message}</span>
                        <span className="recent-activity__timestamp">{activity.timestamp}</span>
                    </li>
                ))}
            </ul>
        </Card>
    );
}
