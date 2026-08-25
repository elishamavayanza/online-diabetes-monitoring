import { useEffect, useState } from 'react';
import { fetchAuditLogs, fetchDataAccessLogs } from '../services/auditService';
import { AuditLog, DataAccessLog } from '../types';

export function useAudit() {
    const [auditLogs, setAuditLogs] = useState<AuditLog[]>([]);
    const [dataAccessLogs, setDataAccessLogs] = useState<DataAccessLog[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            try {
                const [audit, access] = await Promise.all([
                    fetchAuditLogs(),
                    fetchDataAccessLogs(),
                ]);
                setAuditLogs(audit);
                setDataAccessLogs(access);
            } catch (err) {
                setError('Impossible de charger les journaux.');
            } finally {
                setIsLoading(false);
            }
        };
        load();
    }, []);

    return { auditLogs, dataAccessLogs, isLoading, error };
}
