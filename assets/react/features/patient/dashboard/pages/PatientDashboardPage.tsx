import { usePatientDashboard } from '../hooks/usePatientDashboard';
import { HealthSummaryCard } from '../components/HealthSummaryCard';
import { NextAppointmentCard } from '../components/NextAppointmentCard';
import { WatchList } from '../components/WatchList';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/patient/dashboard/_dashboard.scss';

export function PatientDashboardPage() {
    const { data, isLoading, error } = usePatientDashboard();

    if (isLoading) return <Spinner />;
    if (error || !data) return <Alert variant="error">{error ?? 'Aucune donnée'}</Alert>;

    return (
        <div className="patient-dashboard-page">
            <div className="patient-dashboard-page__header">
                <h1>Bonjour {data.patientName}</h1>
            </div>

            <div className="patient-dashboard-page__grid">
                <HealthSummaryCard metrics={data.metrics} />
                <NextAppointmentCard appointment={data.nextAppointment} medication={data.nextMedication} />
                <WatchList items={data.watchList} />
            </div>
        </div>
    );
}
