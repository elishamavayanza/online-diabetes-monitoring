import React from 'react';
import { useParams } from 'react-router-dom';
import { useMedicalRecord } from '../hooks/useMedicalRecord';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Card } from '@/react/components/UI/Card';

export function ClinicianPatientRecordPage() {
    const { patientId } = useParams<{ patientId: string }>();
    const { record, isLoading, error } = useMedicalRecord(patientId!);

    if (isLoading) return <Spinner />;
    if (error || !record || record.status !== 'open') {
        return <Alert variant="error">{error ?? 'Dossier introuvable ou fermé.'}</Alert>;
    }

    return (
        <div className="clinician-record-page">
            <h1>Dossier médical</h1>
            <p>Patient ID : {patientId}</p>
            <Card>
                <h2>Informations générales</h2>
                <p><strong>Taille :</strong> {record.heightCm ?? '—'} cm</p>
                <p><strong>Poids :</strong> {record.weightKg ?? '—'} kg</p>
                <p><strong>Groupe sanguin :</strong> {record.bloodType ?? '—'}</p>
                <p><strong>Allergies :</strong> {record.allergies?.join(', ') ?? 'Aucune'}</p>
                <p><strong>Diagnostics :</strong> {record.diagnoses?.join(', ') ?? 'Aucun'}</p>
            </Card>
        </div>
    );
}
