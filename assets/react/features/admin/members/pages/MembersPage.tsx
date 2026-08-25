import { useMembers } from '../hooks/useMembers';
import { MembersTable } from '../components/MembersTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/admin/members/_members.scss';

export function MembersPage() {
    const { members, isLoading, error } = useMembers();

    if (isLoading) {
        return <Spinner />;
    }

    if (error) {
        return <Alert variant="error">{error}</Alert>;
    }

    return (
        <div className="members-page">
            <div className="members-page__header">
                <h1>Membres</h1>
                <p>Personnes appartenant à l'organisation</p>
            </div>
            <MembersTable members={members} />
        </div>
    );
}
