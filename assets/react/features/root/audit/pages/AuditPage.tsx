import { useState } from 'react';
import { useAudit } from '../hooks/useAudit';
import { AuditLogsTable } from '../components/AuditLogsTable';
import { DataAccessLogsTable } from '../components/DataAccessLogsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/root/audit/_audit.scss';

export function AuditPage() {
    const { auditLogs, dataAccessLogs, isLoading, error } = useAudit();
    const [modalOpen, setModalOpen] = useState(false);
    const { pushAction } = useActionHistory();

    const openDetails = () => {
        setModalOpen(true);
        // Action inverse : fermer la modale
        pushAction(() => setModalOpen(false));
    };

    if (isLoading) {
        return <Spinner />;
    }

    if (error) {
        return <Alert variant="error">{error}</Alert>;
    }

    return (
        <div className="audit-page">
            <div className="audit-page__header">
                <h1>Journaux d’audit</h1>
                <p>Consultez les actions et accès sensibles</p>
                <Button onClick={openDetails}>Ouvrir détails</Button>
            </div>
            <div className="audit-page__content">
                <AuditLogsTable logs={auditLogs} />
                <DataAccessLogsTable logs={dataAccessLogs} />
            </div>

            {modalOpen && (
                <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)}>
                    <p>Détails de l'audit (exemple).</p>
                </Modal>
            )}
        </div>
    );
}
