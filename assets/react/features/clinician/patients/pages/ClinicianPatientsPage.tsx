import { useClinicianPatients } from '../hooks/useClinicianPatients';
import { PatientsTable } from '../components/PatientsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Input } from '@/react/components/Forms/Input';
import '@/styles/pages/clinician/patients/_patients.scss';

export function ClinicianPatientsPage() {
    const { patients, search, setSearch, isLoading, error } = useClinicianPatients();

    if (isLoading) {
        return <Spinner />;
    }

    if (error) {
        return <Alert variant="error">{error}</Alert>;
    }

    return (
        <div className="clinician-patients-page">
            <div className="clinician-patients-page__header">
                <h1>Mes patients</h1>
                <Input
                    placeholder="Rechercher un patient..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                />
            </div>
            <PatientsTable patients={patients} />
        </div>
    );
}
