import { useRoles } from '../hooks/useRoles';
import { PermissionsList } from '../components/PermissionsList';
import { UsersByRoleTable } from '../components/UsersByRoleTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import '@/styles/pages/root/roles/_roles.scss';

export function RolesPage() {
    const { roles, selectedRole, selectedRoleId, setSelectedRoleId, users, isLoading, error } = useRoles();

    if (isLoading) {
        return <Spinner />;
    }

    if (error || !selectedRole) {
        return <Alert variant="error">{error ?? 'Aucun rôle sélectionné.'}</Alert>;
    }

    const tabs = roles.map((role) => ({
        id: role.id,
        label: role.label,
    }));

    return (
        <div className="roles-page">
            <div className="roles-page__header">
                <h1>Rôles & permissions</h1>
                <p>Gérez les rôles et leurs permissions</p>
            </div>

            <Tabs
                tabs={tabs}
                defaultActiveTabId={selectedRoleId}
                onChange={(id) => setSelectedRoleId(id as typeof selectedRoleId)}
            />

            <div className="roles-page__details">
                <PermissionsList permissions={selectedRole.permissions} />
                <UsersByRoleTable users={users} />
            </div>
        </div>
    );
}
