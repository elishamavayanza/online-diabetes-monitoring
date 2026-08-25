export type MeasurementType = 'Glycémie' | 'Tension' | 'Poids' | 'HbA1c' | 'Activité';

export interface MeasurementRecord {
    id: string;
    date: string;
    value: string;
    note?: string;
}

export interface MeasurementsData {
    type: MeasurementType;
    records: MeasurementRecord[];
}
