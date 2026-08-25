import { useTreatments } from '../hooks/useTreatments';
import { TreatmentsList } from '../components/TreatmentsList';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/patient/treatments/_treatments.scss';

export function TreatmentsPage() {
    const { treatments, isLoading, error } = useTreatments();

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="treatments-page">
            <div className="treatments-page__header">
                <h1>Mes traitements</h1>
                <p>Ce qui vous est prescrit</p>
            </div>
            <TreatmentsList treatments={treatments} />
        </div>
    );
}
