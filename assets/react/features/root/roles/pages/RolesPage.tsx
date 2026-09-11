import { useRoles } from '../hooks/useRoles';
import { PermissionsList } from '../components/PermissionsList';
import { UsersByRoleTable } from '../components/UsersByRoleTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { useI18n } from '@/react/i18n/I18nContext';
import '@/styles/pages/root/roles/_roles.scss';

export function RolesPage() {
    const { t } = useI18n();
    const { roles, selectedRole, selectedRoleId, setSelectedRoleId, users, isLoading, error } = useRoles();
    const { pushAction } = useActionHistory();

    const handleRoleChange = (newRoleId: string) => {
        const previousRoleId = selectedRoleId;
        setSelectedRoleId(newRoleId as typeof selectedRoleId);
        // Action inverse : revenir au rôle précédent
        pushAction(() => setSelectedRoleId(previousRoleId));
    };

    if (isLoading) {
        return <Spinner />;
    }

    if (error || !selectedRole) {
        return <Alert variant="error">{error ?? t('Aucun rôle sélectionné.')}</Alert>;
    }

    const tabs = roles.map((role) => ({
        id: role.id,
        label: role.label,
    }));

    return (
        <div className="roles-page">
            <div className="roles-page__header">
                <h1>{t('Rôles & permissions')}</h1>
                <p>{t('Gérez les rôles et leurs permissions')}</p>
            </div>

            <Tabs
                tabs={tabs}
                defaultActiveTabId={selectedRoleId}
                onChange={handleRoleChange}
            />

            <div className="roles-page__details">
                <PermissionsList permissions={selectedRole.permissions} />
                <UsersByRoleTable users={users} />
            </div>
        </div>
    );
}
