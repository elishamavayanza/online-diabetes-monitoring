import { usePatients } from '../hooks/usePatients';
import { PatientsFilter } from '../components/PatientsFilter';
import { PatientsTable } from '../components/PatientsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import '@/styles/pages/admin/patients/_patients.scss';

export function PatientsPage() {
    const { patients, filters, setFilters, isLoading, error } = usePatients();

    if (isLoading) {
        return <Spinner />;
    }

    if (error) {
        return <Alert variant="error">{error}</Alert>;
    }

    return (
        <div className="patients-page">
            <div className="patients-page__header">
                <h1>Patients</h1>
                <Button variant="primary" onClick={() => console.log('Ajouter patient')}>
                    + Ajouter un patient
                </Button>
            </div>
            <PatientsFilter filters={filters} onChange={setFilters} />
            <PatientsTable patients={patients} />
        </div>
    );
}
