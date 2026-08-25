import { useMedicalRecord } from '../hooks/useMedicalRecord';
import { MedicalRecordSections } from '../components/MedicalRecordSections';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/patient/medical-record/_record.scss';

export function MedicalRecordPage() {
    const { data, isLoading, error } = useMedicalRecord();

    if (isLoading) return <Spinner />;
    if (error || !data) return <Alert variant="error">{error ?? 'Aucune donnée'}</Alert>;

    return (
        <div className="medical-record-page">
            <div className="medical-record-page__header">
                <h1>Mon dossier</h1>
                <p>Vos informations médicales personnelles</p>
            </div>
            <MedicalRecordSections data={data} />
        </div>
    );
}
