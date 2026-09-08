import { useIsMobile } from './useIsMobile';
import { useIsPortrait } from './useIsPortrait';
import { useDeviceType } from './useDeviceType';

export function useIsCompact(): boolean {
    const isMobile = useIsMobile();
    const isPortrait = useIsPortrait();
    const deviceType = useDeviceType();

    return isMobile || isPortrait || deviceType === 'tablet';
}