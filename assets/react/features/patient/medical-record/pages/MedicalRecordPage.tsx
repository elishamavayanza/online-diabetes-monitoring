import { useState } from 'react';
import { useMedicalRecord } from '../hooks/useMedicalRecord';
import { MedicalRecordSections } from '../components/MedicalRecordSections';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/patient/medical-record/_record.scss';

export function MedicalRecordPage() {
    const { data, isLoading, error } = useMedicalRecord();
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error || !data) return <Alert variant="error">{error ?? 'Aucune donnée'}</Alert>;

    return (
        <div className="medical-record-page">
            <div className="medical-record-page__header">
                <h1>Mon dossier</h1>
                <p>Vos informations médicales personnelles</p>
                <Button variant="secondary" onClick={openHelp}>Aide</Button>
            </div>
            <MedicalRecordSections data={data} />

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>Ce dossier regroupe vos informations médicales personnelles.</p>
                </Modal>
            )}
        </div>
    );
}
