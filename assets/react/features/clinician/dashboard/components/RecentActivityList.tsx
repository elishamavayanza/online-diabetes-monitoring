import { Card } from '@/react/components/UI/Card';
import { useI18n } from '@/react/i18n/I18nContext';
import { RecentActivity } from '../types';

interface RecentActivityListProps {
    activities: RecentActivity[];
}

export function RecentActivityList({ activities }: RecentActivityListProps) {
    const { t } = useI18n();

    return (
        <Card className="recent-activity">
            <h2 className="section-title">{t('Activité récente')}</h2>
            <ul className="recent-activity__list">
                {activities.map((activity) => (
                    <li key={activity.id} className="recent-activity__item">
                        <span className="recent-activity__message">{activity.message}</span>
                        <span className="recent-activity__timestamp">{activity.timestamp}</span>
                    </li>
                ))}
            </ul>
        </Card>
    );
}
