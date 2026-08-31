import { useParams } from 'react-router-dom';
import { PatientDossierLayout } from '../components/PatientDossierLayout';
import '@/styles/pages/clinician/patients/_record.scss';
import '@/styles/pages/admin/reports/_reports.scss';

export function ClinicianPatientRecordPage() {
    const { patientId } = useParams<{ patientId: string }>();
    return <PatientDossierLayout patientId={patientId!} mode="open" />;
}
