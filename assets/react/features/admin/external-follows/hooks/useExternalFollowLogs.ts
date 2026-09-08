import { useCallback, useEffect, useState } from 'react';
import { ExternalFollowLogEntry } from '../types/types';
import { fetchExternalFollowLogs } from '../services/externalFollowsService';

export function useExternalFollowLogs(organizationId: string, invitationId: string | null) {
    const [logs, setLogs] = useState<ExternalFollowLogEntry[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const refetch = useCallback(async () => {
        if (!organizationId || !invitationId) {
            setLogs([]);
            setIsLoading(false);
            return;
        }
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchExternalFollowLogs(organizationId, invitationId);
            setLogs(data);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Erreur lors de la récupération du journal');
        } finally {
            setIsLoading(false);
        }
    }, [organizationId, invitationId]);

    useEffect(() => {
        void refetch();
    }, [refetch]);

    return { logs, isLoading, error, refetch };
}