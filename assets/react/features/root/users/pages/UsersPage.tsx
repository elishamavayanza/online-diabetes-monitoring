import { useState } from 'react';
import { useUsers } from '../hooks/useUsers';
import { UsersTable } from '../components/UsersTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import { Button } from '@/react/components/UI/Button';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/root/users/_users.scss';
import { UserFormModal } from '../components/UserFormModal';

const FilterIcon = () => (
    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2">
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
    </svg>
);

export function UsersPage() {
    const { users, filter, setFilter, isLoading, error } = useUsers();
    const { pushAction } = useActionHistory();
    const [search, setSearch] = useState('');
    const [createModalOpen, setCreateModalOpen] = useState(false);
    const [orgFilter, setOrgFilter] = useState<string>('');
    const [showOrgFilter, setShowOrgFilter] = useState(false);


    const tabs = [
        { id: 'Tous', label: 'Tous' },
        { id: 'Professionnels', label: 'Professionnels' },
        { id: 'Patients', label: 'Patients' },
        { id: 'Administrateurs', label: 'Administrateurs' },
    ];

    // Extraire la liste unique des organisations
    const organisations = Array.from(
        new Set(users.map((u) => u.organisation).filter((org): org is string => !!org))
    );

    const handleFilterChange = (newFilter: string) => {
        const previousFilter = filter;
        setFilter(newFilter as typeof filter);
        pushAction(() => setFilter(previousFilter));
    };

    const openCreateModal = () => {
        setCreateModalOpen(true);
        pushAction(() => setCreateModalOpen(false));
    };

    // Filtrer les utilisateurs selon la recherche et le filtre organisation
    const filteredUsers = users.filter((user) => {
        const q = search.toLowerCase();
        const matchSearch =
            user.nom.toLowerCase().includes(q) ||
            user.email.toLowerCase().includes(q) ||
            user.type.toLowerCase().includes(q) ||
            (user.organisation && user.organisation.toLowerCase().includes(q));

        const matchOrg = orgFilter ? user.organisation === orgFilter : true;

        return matchSearch && matchOrg;
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
                defaultActiveTabId={filter}
                onChange={handleFilterChange}
            />
            <UsersTable users={filteredUsers} />

            <Modal isOpen={createModalOpen} onClose={() => setCreateModalOpen(false)}>
                <p>Formulaire de création d'utilisateur à implémenter.</p>
            </Modal>

            <UserFormModal
                isOpen={createModalOpen}
                onClose={() => setCreateModalOpen(false)}
                mode="create"
            />
        </div>
    );
}
