import { useClinicianPatients } from '../hooks/useClinicianPatients';
import { PatientsTable } from '../components/PatientsTable';
import { ExternalFollowPatientsCards } from '@/react/features/external-follows/components/ExternalFollowPatientsCards';
import { useMyExternalFollows } from '@/react/features/external-follows/hooks/useMyExternalFollows';
import { Tabs } from '@/react/components/Navigation/Tabs';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/clinician/patients/_patients.scss';

export function ClinicianPatientsPage() {
    const { patients, search, setSearch, isLoading, error } = useClinicianPatients();
    const { follows: externalFollows, isLoading: externalLoading } = useMyExternalFollows();
    const { pushAction } = useActionHistory();

    const handleSearchChange = (newSearch: string) => {
        const previousSearch = search;
        setSearch(newSearch);
        pushAction(() => setSearch(previousSearch));
    };

    const searchBar = (
        <div className="clinician-patients-page__search-wrapper">
            <SearchInput
                fullWidth
                placeholder="Rechercher un patient..."
                value={search}
                onSearch={handleSearchChange}
            />
        </div>
    );

    const ownPatientsContent = (
        <>
            {isLoading && <Spinner />}
            {!isLoading && error && <Alert variant="error">{error}</Alert>}
            {!isLoading && !error && <PatientsTable patients={patients} />}
        </>
    );

    const hasExternalPatients = externalFollows.length > 0;

    if (externalLoading) {
        return (
            <div className="clinician-patients-page">
                <div className="clinician-patients-page__header">
                    <h1>Mes patients</h1>
                    <p>Suivez et gérez vos patients assignés.</p>
                </div>
                {searchBar}
                {ownPatientsContent}
            </div>
        );
    }

    if (!hasExternalPatients) {
        return (
            <div className="clinician-patients-page">
                <div className="clinician-patients-page__header">
                    <h1>Mes patients</h1>
                    <p>Suivez et gérez vos patients assignés.</p>
                </div>
                {searchBar}
                {ownPatientsContent}
            </div>
        );
    }

    return (
        <div className="clinician-patients-page">
            <div className="clinician-patients-page__header">
                <h1>Mes patients</h1>
                <p>Suivez et gérez vos patients assignés.</p>
            </div>

            <Tabs
                variant="underline"
                defaultActiveTabId="all"
                tabs={[
                    { id: 'all', label: 'Tout' },
                    { id: 'own', label: 'Mes patients' },
                    { id: 'external', label: 'Suivis externes' },
                ]}
                renderContent={(tabId) => {
                    if (tabId === 'external') {
                        return (
                            <>
                                {searchBar}
                                <ExternalFollowPatientsCards rolePrefix="clinician" search={search} />
                            </>
                        );
                    }
                    if (tabId === 'own') {
                        return (
                            <>
                                {searchBar}
                                {ownPatientsContent}
                            </>
                        );
                    }
                    return (
                        <>
                            {searchBar}
                            {ownPatientsContent}
                            <ExternalFollowPatientsCards rolePrefix="clinician" search={search} showEmptyState={false} />
                        </>
                    );
                }}
            />
        </div>
    );
}