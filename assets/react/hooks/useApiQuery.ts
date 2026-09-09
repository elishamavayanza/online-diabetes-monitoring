import { useCallback, useEffect, useRef, useState } from 'react';
import { ErrorStateModel, toErrorState } from '@/services/api/errorDisplay';

export interface UseApiQueryOptions {
    /** Message de repli si l'erreur ne peut pas être catégorisée. */
    fallbackMessage?: string;
    /** Callback optionnel (ex. toast) déclenché en cas d'erreur. */
    onError?: (error: unknown) => void;
    /** Désactiver le chargement automatique au montage. */
    enabled?: boolean;
}

/**
 * Hook générique : exécute une requête API, expose l'état
 * (data / isLoading / error normalisé) et un `reload` pour
 * relancer la requête sans recharger toute l'application.
 */
export function useApiQuery<T>(
    fetcher: () => Promise<T>,
    options: UseApiQueryOptions = {},
) {
    const fetcherRef = useRef(fetcher);
    fetcherRef.current = fetcher;
    const optionsRef = useRef(options);
    optionsRef.current = options;

    const [data, setData] = useState<T | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<ErrorStateModel | null>(null);

    const reload = useCallback(async () => {
        if (optionsRef.current.enabled === false) {
            setIsLoading(false);
            return;
        }
        setIsLoading(true);
        setError(null);
        try {
            const result = await fetcherRef.current();
            setData(result);
        } catch (err) {
            const state = toErrorState(err, {
                fallbackMessage: optionsRef.current.fallbackMessage,
            });
            setError(state);
            optionsRef.current.onError?.(err);
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        void reload();
    }, [reload]);

    return { data, isLoading, error, reload };
}