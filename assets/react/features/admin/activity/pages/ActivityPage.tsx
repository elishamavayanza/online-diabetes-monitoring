import { useState } from 'react';
import { useActivity } from '../hooks/useActivity';
import { ActivityList } from '../components/ActivityList';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/admin/activity/_activity.scss';

export function ActivityPage() {
    const { activities, isLoading, error } = useActivity();
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="activity-page">
            <div className="activity-page__header">
                <h1>Activité</h1>
                <p>Journal des événements récents</p>
                <Button variant="secondary" onClick={openHelp}>Aide</Button>
            </div>
            <ActivityList activities={activities} />

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>Cette page affiche l'activité récente de votre organisation.</p>
                </Modal>
            )}
        </div>
    );
}
