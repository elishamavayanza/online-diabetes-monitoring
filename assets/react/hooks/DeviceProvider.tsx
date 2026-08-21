import React, { createContext, useContext } from 'react';
import type { DeviceType } from '../services/device';
import {useDeviceType} from "@/react/hooks/useDeviceType";

const DeviceContext = createContext<DeviceType>('desktop');

export function DeviceProvider({ children }: { children: React.ReactNode }) {
    const deviceType = useDeviceType();
    return <DeviceContext.Provider value={deviceType}>{children}</DeviceContext.Provider>;
}

export function useDevice() {
    return useContext(DeviceContext);
}
