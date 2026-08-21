export type DeviceType = 'mobile' | 'tablet' | 'desktop';

const MOBILE_MAX_WIDTH = 768;
const TABLET_MAX_WIDTH = 992;

export function isMobileWidth(): boolean {
    return window.innerWidth <= MOBILE_MAX_WIDTH;
}

export function isTabletWidth(): boolean {
    return window.innerWidth > MOBILE_MAX_WIDTH && window.innerWidth <= TABLET_MAX_WIDTH;
}

export function getDeviceType(): DeviceType {
    if (isMobileWidth()) return 'mobile';
    if (isTabletWidth()) return 'tablet';
    return 'desktop';
}
