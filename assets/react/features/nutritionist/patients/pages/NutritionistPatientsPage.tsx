import { useNutritionistPatients } from '../hooks/useNutritionistPatients';
import { NutritionistPatientsTable } from '../components/NutritionistPatientsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Input } from '@/react/components/Forms/Input';
import '@/styles/pages/nutritionist/patients/_patients.scss';

export function NutritionistPatientsPage() {
    const { patients, search, setSearch, isLoading, error } = useNutritionistPatients();

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="nutritionist-patients-page">
            <div className="nutritionist-patients-page__header">
                <h1>Mes patients</h1>
                <Input
                    placeholder="Rechercher un patient..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                />
            </div>
            <NutritionistPatientsTable patients={patients} />
        </div>
    );
}
