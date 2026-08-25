import { useProfessionals } from '../hooks/useProfessionals';
import { ProfessionalsTable } from '../components/ProfessionalsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import '@/styles/pages/admin/professionals/_professionals.scss';

export function ProfessionalsPage() {
    const { professionals, isLoading, error } = useProfessionals();

    if (isLoading) {
        return <Spinner />;
    }

    if (error) {
        return <Alert variant="error">{error}</Alert>;
    }

    return (
        <div className="professionals-page">
            <div className="professionals-page__header">
                <h1>Professionnels</h1>
                <Button variant="primary" onClick={() => console.log('Ajouter professionnel')}>
                    + Ajouter un professionnel
                </Button>
            </div>
            <ProfessionalsTable professionals={professionals} />
        </div>
    );
}
