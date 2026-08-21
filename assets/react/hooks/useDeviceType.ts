import { useEffect, useState } from 'react';
import { getDeviceType, DeviceType } from '../services/device';

export function useDeviceType(): DeviceType {
    const [deviceType, setDeviceType] = useState<DeviceType>(getDeviceType);

    useEffect(() => {
        const handleResize = () => setDeviceType(getDeviceType());
        window.addEventListener('resize', handleResize);
        setDeviceType(getDeviceType());
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    return deviceType;
}
