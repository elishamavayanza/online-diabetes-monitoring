import { useCallback, useEffect, useState } from 'react';
import { ExternalFollowInvitation } from '../types/types';
import { fetchExternalFollows } from '../services/externalFollowsService';

export function useExternalFollows(organizationId: string | null) {
    const [invitations, setInvitations] = useState<ExternalFollowInvitation[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const refetch = useCallback(async () => {
        if (!organizationId) {
            setInvitations([]);
            setIsLoading(false);
            return;
        }
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchExternalFollows(organizationId);
            setInvitations(data);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Erreur lors de la récupération des invitations');
        } finally {
            setIsLoading(false);
        }
    }, [organizationId]);

    useEffect(() => {
        void refetch();
    }, [refetch]);

    return { invitations, isLoading, error, refetch };
}