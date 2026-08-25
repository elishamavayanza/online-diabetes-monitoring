import { useEstablishments } from '../hooks/useEstablishments';
import { EstablishmentsTable } from '../components/EstablishmentsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import '@/styles/pages/admin/establishments/_establishments.scss';

export function EstablishmentsPage() {
    const { establishments, isLoading, error } = useEstablishments();

    if (isLoading) {
        return <Spinner />;
    }

    if (error) {
        return <Alert variant="error">{error}</Alert>;
    }

    return (
        <div className="establishments-page">
            <div className="establishments-page__header">
                <h1>Établissements</h1>
                <Button variant="primary" onClick={() => console.log('Ajouter établissement')}>
                    + Ajouter un établissement
                </Button>
            </div>
            <EstablishmentsTable establishments={establishments} />
        </div>
    );
}
