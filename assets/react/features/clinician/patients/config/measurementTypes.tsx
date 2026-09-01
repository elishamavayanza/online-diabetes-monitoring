// measurementTypes.ts
import React from 'react';
import {
    BloodGlucoseIcon,
    BloodPressureIcon,
    Hba1cIcon,
    WeightIcon,
    PhysicalActivityIcon,
    LaboratoryIcon,
} from './measurementIcons';
import { MeasurementTypeId } from '../types';

export interface MeasurementTypeConfig {
    id: MeasurementTypeId;
    label: string;
    description: string;
    unit: string;
    icon: React.ReactNode; // ✅ ReactNode, pas le composant lui-même
}

export const MEASUREMENT_TYPES: MeasurementTypeConfig[] = [
    { id: 'bloodGlucose', label: 'Glycémie', description: 'Taux de glucose sanguin', unit: 'mg/dL', icon: <BloodGlucoseIcon /> },
    { id: 'bloodPressure', label: 'Tension artérielle', description: 'Pression systolique / diastolique', unit: 'mmHg', icon: <BloodPressureIcon /> },
    { id: 'hba1c', label: 'HbA1c', description: 'Hémoglobine glyquée', unit: '%', icon: <Hba1cIcon /> },
    { id: 'weight', label: 'Poids', description: 'Poids corporel et IMC', unit: 'kg', icon: <WeightIcon /> },
    { id: 'physicalActivity', label: 'Activité physique', description: 'Durée et type d\'activité', unit: 'min', icon: <PhysicalActivityIcon /> },
    { id: 'laboratory', label: 'Laboratoire', description: 'Résultats d\'analyses', unit: '', icon: <LaboratoryIcon /> },
];

export function getMeasurementType(id: MeasurementTypeId): MeasurementTypeConfig {
    return MEASUREMENT_TYPES.find((t) => t.id === id) ?? MEASUREMENT_TYPES[0];
}
