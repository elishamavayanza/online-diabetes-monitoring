import { useActivity } from '../hooks/useActivity';
import { ActivityList } from '../components/ActivityList';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/admin/activity/_activity.scss';

export function ActivityPage() {
    const { activities, isLoading, error } = useActivity();

    if (isLoading) {
        return <Spinner />;
    }

    if (error) {
        return <Alert variant="error">{error}</Alert>;
    }

    return (
        <div className="activity-page">
            <div className="activity-page__header">
                <h1>Activité</h1>
                <p>Journal des événements récents</p>
            </div>
            <ActivityList activities={activities} />
        </div>
    );
}
