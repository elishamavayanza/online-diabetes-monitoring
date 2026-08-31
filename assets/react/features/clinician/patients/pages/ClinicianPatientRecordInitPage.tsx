import React from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useMedicalRecord } from '../hooks/useMedicalRecord';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Card } from '@/react/components/UI/Card';

export function ClinicianPatientRecordInitPage() {
    const { patientId } = useParams<{ patientId: string }>();
    const navigate = useNavigate();
    const { record, isLoading, isSaving, error, create, reopen } = useMedicalRecord(patientId!);

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    const isClosed = record?.status === 'closed';
    const hasNoRecord = !record || record.status === 'none';

    const handleCreate = async () => {
        const success = await create();
        if (success) {
            navigate(`/clinician/patients/${patientId}/record`);
        }
    };

    const handleReopen = async () => {
        const success = await reopen();
        if (success) {
            navigate(`/clinician/patients/${patientId}/record`);
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
