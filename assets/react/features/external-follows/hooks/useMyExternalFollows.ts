import { useCallback, useEffect, useState } from 'react';
import { ExternalFollowInvitation } from '../types/types';
import { fetchMyExternalFollows } from '../services/externalFollowsService';

export function useMyExternalFollows() {
    const [follows, setFollows] = useState<ExternalFollowInvitation[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const refetch = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const data = await fetchMyExternalFollows();
            setFollows(data);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Erreur lors de la récupération de vos suivis externes.');
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        void refetch();
    }, [refetch]);

    return { follows, isLoading, error, refetch };
}