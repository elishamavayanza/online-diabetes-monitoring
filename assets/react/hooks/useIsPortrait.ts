import { useEffect, useState } from 'react';

export function useIsPortrait(): boolean {
    const [isPortrait, setIsPortrait] = useState<boolean>(
        () => window.innerHeight > window.innerWidth
    );

    useEffect(() => {
        const handleResize = () => {
            setIsPortrait(window.innerHeight > window.innerWidth);
        };

        window.addEventListener('resize', handleResize);
        // Vérification initiale
        handleResize();

        return () => window.removeEventListener('resize', handleResize);
    }, []);

    return isPortrait;
}
