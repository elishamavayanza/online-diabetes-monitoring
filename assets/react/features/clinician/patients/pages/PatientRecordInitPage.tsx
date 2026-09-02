import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useMedicalRecord } from '../hooks/useMedicalRecord';
import { fetchPatientProfile } from '../services/clinicianPatientsService';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Card } from '@/react/components/UI/Card';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/clinician/patients/_record.scss';

interface PatientRecordInitPageProps {
    basePath?: string;
}

export function PatientRecordInitPage({ basePath = '/clinician' }: PatientRecordInitPageProps) {
    const { patientId } = useParams<{ patientId: string }>();
    const navigate = useNavigate();
    const { record, isLoading, isSaving, error, create, reopen } = useMedicalRecord(patientId!);
    const [organizationId, setOrganizationId] = useState<string | undefined>();
    const [profileError, setProfileError] = useState<string | null>(null);
    const { pushAction } = useActionHistory();

    const isClosed = record?.status === 'closed';
    const hasNoRecord = !record || record.status === 'none';
    const isOpen = record?.status === 'open';

    useEffect(() => {
        if (!patientId) return;
        fetchPatientProfile(patientId)
            .then((profile) => {
                if (profile.organizationId) {
                    setOrganizationId(String(profile.organizationId));
                }
            })
            .catch(() => setProfileError('Impossible de charger le profil patient.'));
    }, [patientId]);

    useEffect(() => {
        if (isOpen) {
            navigate(`${basePath}/patients/${patientId}/record`, { replace: true });
        }
    }, [isOpen, navigate, patientId, basePath]);

    if (isLoading) return <Spinner />;
    if (error || profileError) return <Alert variant="error">{error ?? profileError}</Alert>;
    if (isOpen) return <Spinner />;

    const handleCreate = async () => {
        const success = await create(organizationId);
        if (success) {
            pushAction(() => navigate(`${basePath}/patients/${patientId}/record/init`));
            navigate(`${basePath}/patients/${patientId}/record`);
        }
    };

    const handleReopen = async () => {
        const success = await reopen();
        if (success) {
            pushAction(() => navigate(`${basePath}/patients/${patientId}/record/init`));
            navigate(`${basePath}/patients/${patientId}/record`);
        }
    };

    return (
        <div className="clinician-record-init-page">
            <h1>Dossier médical</h1>
            <Card>
                {hasNoRecord && (
                    <>
                        <p>Aucun dossier médical n'a été créé pour ce patient.</p>
                        <Button
                            variant="primary"
                            onClick={handleCreate}
                            disabled={isSaving}
                        >
                            {isSaving ? 'Création...' : 'Créer un dossier'}
                        </Button>
                    </>
                )}
                {isClosed && (
                    <>
                        <p>Le dossier médical de ce patient est fermé.</p>
                        <Button
                            variant="primary"
                            onClick={handleReopen}
                            disabled={isSaving}
                        >
                            {isSaving ? 'Réouverture...' : 'Rouvrir le dossier'}
                        </Button>
                    </>
                )}
            </Card>
        </div>
    );
}
