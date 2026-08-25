import { useMeasurements } from '../hooks/useMeasurements';
import { MeasurementsTable } from '../components/MeasurementsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Tabs } from '@/react/components/Navigation/Tabs';
import '@/styles/pages/patient/health/_measurements.scss';

export function MeasurementsPage() {
    const { type, setType, records, isLoading, error } = useMeasurements();

    const tabs = [
        { id: 'Glycémie', label: 'Glycémie' },
        { id: 'Tension', label: 'Tension' },
        { id: 'Poids', label: 'Poids' },
        { id: 'HbA1c', label: 'HbA1c' },
        { id: 'Activité', label: 'Activité' },
    ];

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="measurements-page">
            <div className="measurements-page__header">
                <h1>Mes mesures</h1>
                <p>Historique de vos données de santé</p>
            </div>
            <Tabs
                tabs={tabs}
                defaultActiveTabId={type}
                onChange={(id) => setType(id as typeof type)}
            />
            <MeasurementsTable records={records} />
        </div>
    );
}
