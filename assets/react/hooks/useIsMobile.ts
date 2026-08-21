import { useEffect, useState } from 'react';
import { isMobileWidth } from '../services/device';

export function useIsMobile(): boolean {
    const [isMobile, setIsMobile] = useState<boolean>(isMobileWidth);

    useEffect(() => {
        const handleResize = () => setIsMobile(isMobileWidth());
        window.addEventListener('resize', handleResize);
        // Vérification initiale au cas où la fenêtre change entre la première exécution et le montage
        setIsMobile(isMobileWidth());
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    return isMobile;
}
