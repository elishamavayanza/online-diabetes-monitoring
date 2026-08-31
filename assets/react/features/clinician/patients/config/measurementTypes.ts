import { MeasurementTypeId } from '../types';

export interface MeasurementTypeConfig {
    id: MeasurementTypeId;
    label: string;
    description: string;
    unit: string;
    icon: string;
}

export const MEASUREMENT_TYPES: MeasurementTypeConfig[] = [
    { id: 'bloodGlucose', label: 'Glycémie', description: 'Taux de glucose sanguin', unit: 'mg/dL', icon: '🩸' },
    { id: 'bloodPressure', label: 'Tension artérielle', description: 'Pression systolique / diastolique', unit: 'mmHg', icon: '💓' },
    { id: 'hba1c', label: 'HbA1c', description: 'Hémoglobine glyquée', unit: '%', icon: '📊' },
    { id: 'weight', label: 'Poids', description: 'Poids corporel et IMC', unit: 'kg', icon: '⚖️' },
    { id: 'physicalActivity', label: 'Activité physique', description: 'Durée et type d\'activité', unit: 'min', icon: '🏃' },
    { id: 'laboratory', label: 'Laboratoire', description: 'Résultats d\'analyses', unit: '', icon: '🔬' },
];

export function getMeasurementType(id: MeasurementTypeId): MeasurementTypeConfig {
    return MEASUREMENT_TYPES.find((t) => t.id === id) ?? MEASUREMENT_TYPES[0];
}
