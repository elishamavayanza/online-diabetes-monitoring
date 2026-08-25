import {MeasurementType, MeasurementsData, MeasurementRecord} from '../types';

export async function fetchMeasurements(type: MeasurementType): Promise<MeasurementRecord[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    // Simulation de données différentes selon le type
    const data: Record<MeasurementType, MeasurementRecord[]> = {
        'Glycémie': [
            { id: '1', date: '2026-08-25', value: '120 mg/dL' },
            { id: '2', date: '2026-08-24', value: '135 mg/dL' },
            { id: '3', date: '2026-08-23', value: '118 mg/dL' },
        ],
        'Tension': [
            { id: '4', date: '2026-08-25', value: '12/8 mmHg' },
            { id: '5', date: '2026-08-24', value: '13/8 mmHg' },
        ],
        'Poids': [
            { id: '6', date: '2026-08-25', value: '72 kg' },
            { id: '7', date: '2026-08-20', value: '71,5 kg' },
        ],
        'HbA1c': [
            { id: '8', date: '2026-07-15', value: '6,8 %' },
            { id: '9', date: '2026-04-10', value: '7,1 %' },
        ],
        'Activité': [
            { id: '10', date: '2026-08-25', value: '8500 pas' },
            { id: '11', date: '2026-08-24', value: '10200 pas' },
        ],
    };

    return data[type] || [];
}
