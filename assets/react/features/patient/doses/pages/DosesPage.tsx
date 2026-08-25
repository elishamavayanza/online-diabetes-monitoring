import { useDoses } from '../hooks/useDoses';
import { DosesList } from '../components/DosesList';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/patient/doses/_doses.scss';

export function DosesPage() {
    const { intakes, isLoading, error } = useDoses();

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="doses-page">
            <div className="doses-page__header">
                <h1>Mes prises</h1>
                <p>Historique réel de vos prises</p>
            </div>
            <DosesList intakes={intakes} />
        </div>
    );
}
