import { useEffect, useState } from 'react';

/**
 * Suit l'état de connexion réseau du navigateur.
 * Retourne `true` si l'appareil est hors-ligne.
 */
export function useOnlineStatus(): boolean {
    const [isOffline, setIsOffline] = useState<boolean>(
        typeof navigator !== 'undefined' ? !navigator.onLine : false
    );

    useEffect(() => {
        const handleOffline = () => setIsOffline(true);
        const handleOnline = () => setIsOffline(false);

        window.addEventListener('offline', handleOffline);
        window.addEventListener('online', handleOnline);

        return () => {
            window.removeEventListener('offline', handleOffline);
            window.removeEventListener('online', handleOnline);
        };
    }, []);

    return isOffline;
}

/**
 * Appel de fonction asynchrone protégé contre les doubles soumissions :
 * ignore les appels pendant qu'une requête est en cours.
 */
export function useSubmitLock<TArgs extends unknown[] = []>() {
    const [submitting, setSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const submit = async (
        fn: (...args: TArgs) => Promise<void>,
        ...args: TArgs
    ): Promise<boolean> => {
        if (submitting) {
            return false;
        }

        setSubmitting(true);
        setError(null);
        try {
            await fn(...args);
            return true;
        } catch (err) {
            const message = err instanceof Error ? err.message : 'Une erreur est survenue.';
            setError(message);
            throw err;
        } finally {
            setSubmitting(false);
        }
    };

    return { submitting, error, setError, submit };
}