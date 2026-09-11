import { useEffect, useState } from 'react';
import { useUsers, UserFilterTab } from '../hooks/useUsers';
import { UsersTable } from '../components/UsersTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/root/users/_users.scss';
import { UserFormModal } from '../components/UserFormModal';
import { UserDetailsDrawer } from '../components/UserDetailsDrawer';
import { AffectationModal } from '../components/AffectationModal';
import { AffectationData } from '../types/affectation';
import { User, UserType } from '../types';
import { fetchOrganisations } from '@/react/features/root/organisations/services/organisationsService';
import { useI18n } from '@/react/i18n/I18nContext';

const FilterIcon = () => (
    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2">
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
    </svg>
);

interface OrganisationOption {
    id: string;
    label: string;
}

// Mapping du type User vers le type du formulaire
function mapUserTypeToFormType(type: UserType): 'patient' | 'professional' {
    if (type === 'Patient') return 'patient';
    return 'professional'; // inclut Professional et Administrator
}

// Convertit un objet User en valeurs initiales pour le formulaire
function mapUserToFormValues(user: User): any {
    const base = {
        email: user.email,
        password: '', // le mot de passe n'est pas récupéré, on laisse vide
        fullName: user.nom,
        phone: '',
        gender: 'UNSPECIFIED',
        locale: 'fr',
        avatarUrl: user.avatarUrl || '',
        avatarFile: null,
        address: {
            street: '',
            city: '',
            postalCode: '',
            country: 'RDC',
        },
    };

    if (user.type === 'Professional' || user.type === 'Administrator') {
        return {
            ...base,
            licenseNumber: '',
            professionalType: 'CLINICIAN',
            specialty: '',
            signatureUrl: '',
        };
    } else {
        return {
            ...base,
            dateOfBirth: '',
            placeOfBirth: '',
            bloodType: '',
            heightCm: '',
        };
    }
}

export function UsersPage() {
    const { t } = useI18n();
    const {
        users,
        total,
        page,
        limit,
        isLoading,
        error,
        filter,
        setFilter,
        setQ,
        setPage,
        setSort,
        setOrg,
    } = useUsers();
    const { pushAction } = useActionHistory();
    const [search, setSearch] = useState('');
    const [createModalOpen, setCreateModalOpen] = useState(false);
    const [orgFilter, setOrgFilter] = useState<string>('');
    const [showOrgFilter, setShowOrgFilter] = useState(false);
    const [organisations, setOrganisations] = useState<OrganisationOption[]>([]);
    const [selectedUser, setSelectedUser] = useState<User | null>(null);
    const [detailsOpen, setDetailsOpen] = useState(false);
    const [formModalOpen, setFormModalOpen] = useState(false);
    const [formMode, setFormMode] = useState<'create' | 'edit'>('create');
    const [editingUser, setEditingUser] = useState<User | null>(null);

    //  Nouveaux états pour l'affectation
    const [affectModalOpen, setAffectModalOpen] = useState(false);
    const [affectMode, setAffectMode] = useState<'create' | 'edit'>('create');
    const [affectData, setAffectData] = useState<AffectationData | undefined>(undefined);
    const [affectUserId, setAffectUserId] = useState('');

    const tabs = [
        { id: 'Tous', label: t('Tous') },
        { id: 'Professionnels', label: t('Professionnels') },
        { id: 'Patients', label: t('Patients') },
        { id: 'Administrateurs', label: t('Administrateurs') },
    ];

    // Liste des organisations pour le filtre (source dédiée, indépendante de la page courante).
    useEffect(() => {
        let cancelled = false;
        fetchOrganisations()
            .then((nodes) => {
                if (!cancelled) {
                    setOrganisations(
                        nodes.map((node) => ({ id: String(node.id), label: node.label })),
                    );
                }
            })
            .catch(() => {
                // Le filtre reste masqué si la liste des organisations est indisponible.
            });
        return () => {
            cancelled = true;
        };
    }, []);

    const handleTabChange = (newTab: string) => {
        const previousTab = filter;
        setFilter(newTab as UserFilterTab);
        pushAction(() => setFilter(previousTab as UserFilterTab));
    };

    const openCreateModal = () => {
        setFormMode('create');
        setEditingUser(null);
        setFormModalOpen(true);
        pushAction(() => setFormModalOpen(false));
    };

    const openEditModal = (user: User) => {
        setFormMode('edit');
        setEditingUser(user);
        setDetailsOpen(false); // fermer le drawer
        setFormModalOpen(true);
        pushAction(() => setFormModalOpen(false));
    };

    const closeDetails = () => {
        setDetailsOpen(false);
        setSelectedUser(null);
    };

    //  Ouverture de la modale d'affectation (création)
    const openCreateAffectation = (user: User) => {
        setAffectUserId(user.id);
        setAffectMode('create');
        setAffectData(undefined);
        setAffectModalOpen(true);
        setDetailsOpen(false);
        pushAction(() => setAffectModalOpen(false));
    };

    //  Ouverture de la modale d'affectation (édition)
    const openEditAffectation = (user: User) => {
        setAffectUserId(user.id);
        setAffectMode('edit');
        // Données simulées à remplacer par un fetch réel
        setAffectData({
            affectationId: 'aff123',
            userId: user.id,
            organizationId: 'org1', // à adapter selon l'organisation actuelle
            facilityId: '',
            departmentId: '',
            startDate: '2026-01-01',
            endDate: '',
            status: 'ACTIVE',
        });
        setAffectModalOpen(true);
        setDetailsOpen(false);
        pushAction(() => setAffectModalOpen(false));
    };

    const handleSuspend = (user: User) => {
        console.log('Suspendre', user);
        // À implémenter : appeler un service de suspension
    };

    const handleOrgSelect = (organisationId: string) => {
        setOrgFilter(organisationId);
        setOrg(organisationId || undefined);
        setShowOrgFilter(false);
    };

    if (error) return <Alert variant="error">{error}</Alert>;
    if (isLoading && users.length === 0) return <Spinner />;

    return (
        <div className="users-page">
            <div className="users-page__header">
                <h1>{t('Utilisateurs')}</h1>
                <p>{t('Gérez les comptes de la plateforme')}</p>
            </div>

            <div className="users-page__actions">
                <SearchInput
                    placeholder={t('Rechercher un utilisateur...')}
                    value={search}
                    onSearch={(value: string) => {
                        setSearch(value);
                        setQ(value);
                    }}
                    className="users-page__search"
                />

                {organisations.length > 0 && (
                    <div className="users-page__filter-wrapper">
                        <button
                            className={`users-page__filter-btn ${orgFilter ? 'users-page__filter-btn--active' : ''}`}
                            onClick={() => setShowOrgFilter((prev) => !prev)}
                            aria-label={t('Filtrer par organisation')}
                            title={t('Filtrer par organisation')}
                        >
                            <FilterIcon />
                        </button>

                        {showOrgFilter && (
                            <div className="users-page__filter-dropdown">
                                <div
                                    className={`users-page__filter-option ${orgFilter === '' ? 'users-page__filter-option--selected' : ''}`}
                                    onClick={() => handleOrgSelect('')}
                                >
                                    {t('Toutes les organisations')}
                                </div>
                                {organisations.map((org) => (
                                    <div
                                        key={org.id}
                                        className={`users-page__filter-option ${orgFilter === org.id ? 'users-page__filter-option--selected' : ''}`}
                                        onClick={() => handleOrgSelect(org.id)}
                                    >
                                        {org.label}
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                )}
            </div>

            <Tabs
                tabs={tabs}
                defaultActiveTabId={filter}
                onChange={handleTabChange}
            />

            <UsersTable
                users={users}
                total={total}
                page={page}
                limit={limit}
                loading={isLoading}
                onPageChange={setPage}
                onSort={setSort}
                onViewDetails={(user) => {
                    setSelectedUser(user);
                    setDetailsOpen(true);
                }}
            />

            {/* Modale de création/édition utilisateur */}
            <UserFormModal
                key={formMode + (editingUser?.id || '')}
                isOpen={formModalOpen}
                onClose={() => setFormModalOpen(false)}
                mode={formMode}
                initialUser={
                    editingUser
                        ? {
                            type: mapUserTypeToFormType(editingUser.type),
                            data: mapUserToFormValues(editingUser),
                        }
                        : undefined
                }
            />

            {/* Drawer de détails */}
            <UserDetailsDrawer
                user={selectedUser}
                isOpen={detailsOpen}
                onClose={closeDetails}
                onAffect={openCreateAffectation}
                onModifyAffectation={openEditAffectation}
                onModify={openEditModal}
                onSuspend={handleSuspend}
            />

            {/* Modale d'affectation */}
            <AffectationModal
                isOpen={affectModalOpen}
                onClose={() => setAffectModalOpen(false)}
                mode={affectMode}
                userId={affectUserId}
                affectationData={affectData}
            />
        </div>
    );
}