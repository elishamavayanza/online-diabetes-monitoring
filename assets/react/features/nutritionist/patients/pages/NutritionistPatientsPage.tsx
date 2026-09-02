import { useNutritionistPatients } from '../hooks/useNutritionistPatients';
import { NutritionistPatientsTable } from '../components/NutritionistPatientsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/clinician/patients/_patients.scss';

export function NutritionistPatientsPage() {
    const { patients, search, setSearch, isLoading, error } = useNutritionistPatients();
    const { pushAction } = useActionHistory();

    const handleSearchChange = (newSearch: string) => {
        const previousSearch = search;
        setSearch(newSearch);
        pushAction(() => setSearch(previousSearch));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="clinician-patients-page">
            <div className="clinician-patients-page__header">
                <h1>Mes patients</h1>
                <p>Suivez et gérez vos patients assignés.</p>
            </div>

            <div className="clinician-patients-page__search-wrapper">
                <SearchInput
                    fullWidth
                    placeholder="Rechercher un patient..."
                    value={search}
                    onSearch={handleSearchChange}
                />
            </div>

            <NutritionistPatientsTable patients={patients} />
        </div>
    );
}
