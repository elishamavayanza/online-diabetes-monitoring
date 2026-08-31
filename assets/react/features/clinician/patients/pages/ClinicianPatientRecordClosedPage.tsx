import React from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useMedicalRecord } from '../hooks/useMedicalRecord';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Card } from '@/react/components/UI/Card';

export function ClinicianPatientRecordClosedPage() {
    const { patientId } = useParams<{ patientId: string }>();
    const navigate = useNavigate();
    const { record, isLoading, isSaving, error, reopen } = useMedicalRecord(patientId!);

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    // Vérifier que le dossier est bien fermé
    if (!record || record.status !== 'closed') {
        return (
            <Alert variant="warning">
                Ce dossier n'est pas actuellement fermé.
            </Alert>
        );
    }

    const handleReopen = async () => {
        const success = await reopen();
        if (success) {
            navigate(`/clinician/patients/${patientId}/record`);
        }
    };

    return (
        <div className="clinician-record-closed-page">
            <h1>Dossier médical fermé</h1>
            <Card>
                <p>
                    Le dossier médical de ce patient est actuellement fermé.
                    Vous pouvez le rouvrir pour consulter ou modifier les informations.
                </p>
                <Button
                    variant="primary"
                    onClick={handleReopen}
                    disabled={isSaving}
                >
                    {isSaving ? 'Réouverture...' : 'Rouvrir le dossier'}
                </Button>
            </Card>
        </div>
    );
}
