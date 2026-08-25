import { useAudit } from '../hooks/useAudit';
import { AuditLogsTable } from '../components/AuditLogsTable';
import { DataAccessLogsTable } from '../components/DataAccessLogsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import '@/styles/pages/root/audit/_audit.scss';

export function AuditPage() {
    const { auditLogs, dataAccessLogs, isLoading, error } = useAudit();

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
            </div>
            <div className="audit-page__content">
                <AuditLogsTable logs={auditLogs} />
                <DataAccessLogsTable logs={dataAccessLogs} />
            </div>
        </div>
    );
}
