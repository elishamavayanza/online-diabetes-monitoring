import { useUsers } from '../hooks/useUsers';
import { UsersTable } from '../components/UsersTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import '@/styles/pages/root/users/_users.scss';

export function UsersPage() {
    const { users, filter, setFilter, isLoading, error } = useUsers();

    const tabs = [
        { id: 'Tous', label: 'Tous' },
        { id: 'Professionnels', label: 'Professionnels' },
        { id: 'Patients', label: 'Patients' },
        { id: 'Administrateurs', label: 'Administrateurs' },
    ];

    if (isLoading) {
        return <Spinner />;
    }

    if (error) {
        return <Alert variant="error">{error}</Alert>;
    }

    return (
        <div className="users-page">
            <div className="users-page__header">
                <h1>Utilisateurs</h1>
                <p>Gérez les comptes de la plateforme</p>
            </div>
            <Tabs
                tabs={tabs}
                defaultActiveTabId={filter}
                onChange={(id) => setFilter(id as typeof filter)}
            />
            <UsersTable users={users} />
        </div>
    );
}
