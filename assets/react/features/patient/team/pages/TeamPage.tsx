import { useState } from 'react';
import { useCareTeam } from '../hooks/useCareTeam';
import { TeamMemberCard } from '../components/TeamMemberCard';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/patient/team/_team.scss';

export function TeamPage() {
    const { members, isLoading, error } = useCareTeam();
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="team-page">
            <div className="team-page__header">
                <h1>Mon équipe</h1>
                <p>Les professionnels qui vous accompagnent</p>
                <Button variant="secondary" onClick={openHelp}>Aide</Button>
            </div>
            <div className="team-page__members">
                {members.map((member) => (
                    <TeamMemberCard key={member.id} member={member} />
                ))}
            </div>

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>Voici les professionnels qui s'occupent de vous.</p>
                </Modal>
            )}
        </div>
    );
}
