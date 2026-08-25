import { useCareTeam } from '../hooks/useCareTeam';
import { TeamMemberCard } from '../components/TeamMemberCard';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/patient/team/_team.scss';

export function TeamPage() {
    const { members, isLoading, error } = useCareTeam();

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="team-page">
            <div className="team-page__header">
                <h1>Mon équipe</h1>
                <p>Les professionnels qui vous accompagnent</p>
            </div>
            <div className="team-page__members">
                {members.map((member) => (
                    <TeamMemberCard key={member.id} member={member} />
                ))}
            </div>
        </div>
    );
}
