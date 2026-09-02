import { useParams } from 'react-router-dom';
import { PatientDossierLayout } from '@/react/features/clinician/patients/components/PatientDossierLayout';
import '@/styles/pages/clinician/patients/_record.scss';

export function NutritionistPatientRecordClosedPage() {
    const { patientId } = useParams<{ patientId: string }>();
    return <PatientDossierLayout patientId={patientId!} mode="closed" basePath="/nutritionist" />;
}
