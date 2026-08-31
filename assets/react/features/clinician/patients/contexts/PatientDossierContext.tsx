import { createContext, useContext, ReactNode } from 'react';
import { MeasurementPeriod, MeasurementTypeId, PatientDossierData } from '../types';

export interface PatientDossierContextValue {
    patientId: string;
    data: PatientDossierData;
    reload: () => void;
    isReadOnly: boolean;
    selectedDate: Date | null;
    setSelectedDate: (date: Date | null) => void;
    period: MeasurementPeriod;
    setPeriod: (period: MeasurementPeriod) => void;
    openMeasurementModal: (type?: MeasurementTypeId) => void;
    openAppointmentModal: () => void;
    openPrescriptionModal: () => void;
    openNoteModal: () => void;
    openMealModal: () => void;
}

const PatientDossierContext = createContext<PatientDossierContextValue | null>(null);

export function PatientDossierProvider({
    value,
    children,
}: {
    value: PatientDossierContextValue;
    children: ReactNode;
}) {
    return (
        <PatientDossierContext.Provider value={value}>
            {children}
        </PatientDossierContext.Provider>
    );
}

export function usePatientDossierContext(): PatientDossierContextValue {
    const ctx = useContext(PatientDossierContext);
    if (!ctx) {
        throw new Error('usePatientDossierContext doit être utilisé dans PatientDossierProvider');
    }
    return ctx;
}
