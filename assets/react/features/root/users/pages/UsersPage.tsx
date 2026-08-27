import { useState } from 'react';
import { useUsers } from '../hooks/useUsers';
import { UsersTable } from '../components/UsersTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import { Button } from '@/react/components/UI/Button';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/root/users/_users.scss';
import { UserFormModal } from '../components/UserFormModal';
import { UserDetailsDrawer } from '../components/UserDetailsDrawer';
import { User } from '../types'; //  import du type User

const FilterIcon = () => (
    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2">
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
    </svg>
);

export function UsersPage() {
    const { users, isLoading, error } = useUsers();
    const { pushAction } = useActionHistory();
    const [search, setSearch] = useState('');
    const [createModalOpen, setCreateModalOpen] = useState(false);
    const [orgFilter, setOrgFilter] = useState<string>('');
    const [showOrgFilter, setShowOrgFilter] = useState(false);
    const [activeTab, setActiveTab] = useState<string>('Tous');
    const [selectedUser, setSelectedUser] = useState<User | null>(null);
    const [detailsOpen, setDetailsOpen] = useState(false);

    const tabs = [
        { id: 'Tous', label: 'Tous' },
        { id: 'Professionnels', label: 'Professionnels' },
        { id: 'Patients', label: 'Patients' },
        { id: 'Administrateurs', label: 'Administrateurs' },
        { id: 'Non affectés', label: 'Non affectés' },
    ];

    // Extraire la liste unique des organisations
    const organisations = Array.from(
        new Set(users.map((u) => u.organisation).filter((org): org is string => !!org))
    );

    const handleTabChange = (newTab: string) => {
        const previousTab = activeTab;
        setActiveTab(newTab);
        pushAction(() => setActiveTab(previousTab));
    };

    const openCreateModal = () => {
        setCreateModalOpen(true);
        pushAction(() => setCreateModalOpen(false));
    };

    const openDetails = (user: User) => {
        setSelectedUser(user);
        setDetailsOpen(true);
    };

    const closeDetails = () => {
        setDetailsOpen(false);
        setSelectedUser(null);
    };

    const handleAffect = (user: User) => {
        console.log('Affecter', user);
        // À implémenter : ouvrir une modale pour choisir l'organisation
    };

    const handleModify = (user: User) => {
        console.log('Modifier', user);
        // À implémenter : ouvrir UserFormModal en mode édition avec les données de l'utilisateur
    };

    const handleSuspend = (user: User) => {
        console.log('Suspendre', user);
        // À implémenter : appeler un service de suspension
    };

    // Filtrer les utilisateurs selon l'onglet actif, la recherche et le filtre organisation
    const filteredUsers = users.filter((user) => {
        const q = search.toLowerCase();
        const matchSearch =
            user.nom.toLowerCase().includes(q) ||
            user.email.toLowerCase().includes(q) ||
            user.type.toLowerCase().includes(q) ||
            (user.organisation && user.organisation.toLowerCase().includes(q));

        const matchOrg = orgFilter ? user.organisation === orgFilter : true;

        let matchTab = true;
        switch (activeTab) {
            case 'Professionnels':
                matchTab = user.type === 'Professional';
                break;
            case 'Patients':
                matchTab = user.type === 'Patient';
                break;
            case 'Administrateurs':
                matchTab = user.type === 'Administrator';
                break;
            case 'Non affectés':
                matchTab = !user.organisation;
                break;
            default:
                matchTab = true;
        }

        return matchSearch && matchOrg && matchTab;
    });

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="users-page">
            <div className="users-page__header">
                <h1>Utilisateurs</h1>
                <p>Gérez les comptes de la plateforme</p>
            </div>

            <div className="users-page__actions">
                <SearchInput
                    placeholder="Rechercher un utilisateur..."
                    value={search}
                    onSearch={(value: string) => setSearch(value)}
                    className="users-page__search"
                />

                {/* Bouton filtre organisation */}
                <div className="users-page__filter-wrapper">
                    <button
                        className={`users-page__filter-btn ${orgFilter ? 'users-page__filter-btn--active' : ''}`}
                        onClick={() => setShowOrgFilter((prev) => !prev)}
                        aria-label="Filtrer par organisation"
                        title="Filtrer par organisation"
                    >
                        <FilterIcon />
                    </button>

                    {showOrgFilter && (
                        <div className="users-page__filter-dropdown">
                            <div
                                className={`users-page__filter-option ${orgFilter === '' ? 'users-page__filter-option--selected' : ''}`}
                                onClick={() => {
                                    setOrgFilter('');
                                    setShowOrgFilter(false);
                                }}
                            >
                                Toutes les organisations
                            </div>
                            {organisations.map((org) => (
                                <div
                                    key={org}
                                    className={`users-page__filter-option ${orgFilter === org ? 'users-page__filter-option--selected' : ''}`}
                                    onClick={() => {
                                        setOrgFilter(org);
                                        setShowOrgFilter(false);
                                    }}
                                >
                                    {org}
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                <Button onClick={openCreateModal} className="users-page__add-btn">
                    Créer un utilisateur
                </Button>
            </div>

            <Tabs
                tabs={tabs}
                defaultActiveTabId={activeTab}
                onChange={handleTabChange}
            />

            <UsersTable
                users={filteredUsers}
                onViewDetails={openDetails}   // ✅ passage de la fonction
            />

            {/* Modale de création */}
            <UserFormModal
                isOpen={createModalOpen}
                onClose={() => setCreateModalOpen(false)}
                mode="create"
            />

            {/* Drawer de détails */}
            <UserDetailsDrawer
                user={selectedUser}
                isOpen={detailsOpen}
                onClose={closeDetails}
                onAffect={handleAffect}
                onModify={handleModify}
                onSuspend={handleSuspend}
            />
        </div>
    );
}
