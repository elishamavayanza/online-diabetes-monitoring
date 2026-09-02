// hooks/usePresence.ts
import { useEffect, useState } from 'react';
import apiClient from '@/services/api/client';
import { unwrapApiData, ApiFeedback } from '@/react/utils/apiFeedback';

export function usePresence(participantId: string, intervalMs = 30000) {
    const [isOnline, setIsOnline] = useState<boolean | null>(null);

    useEffect(() => {
        let cancelled = false;
        const fetchPresence = async () => {
            try {
                const response = await apiClient.get<ApiFeedback<{ isOnline: boolean }>>(
                    `/users/${participantId}/presence`
                );
                const data = unwrapApiData(response.data, 'Erreur présence');
                if (!cancelled) setIsOnline(data.isOnline);
            } catch {
                if (!cancelled) setIsOnline(false);
            }
        };

        fetchPresence();
        const interval = setInterval(fetchPresence, intervalMs);
        return () => {
            cancelled = true;
            clearInterval(interval);
        };
    }, [participantId, intervalMs]);

    return isOnline;
}
