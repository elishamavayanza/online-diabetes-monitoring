// src/react/features/admin/appointments/data/options.ts
import { SelectOption } from '@/react/features/admin/appointments/types/types';

export const patientOptions: SelectOption[] = [
    { value: '4a613328-98e3-4d64-8898-0c06a3861c8f', label: 'Dupont Jean' },
    { value: 'b7f1a4e4-5ec4-4bc1-8822-7b5b9b5a3a2e', label: 'Martin Sophie' },
];

export const professionalOptions: SelectOption[] = [
    { value: '7b224119-12f4-4b53-9912-1f83c2748a12', label: 'Dr. Lefevre' },
    { value: 'f1d2c3b4-5e6f-7a8b-9c0d-1e2f3a4b5c6d', label: 'Dr. Moreau' },
];

export const organizationOptions: SelectOption[] = [
    { value: '1c552144-88ef-4a92-b4c4-7893a12b4e55', label: 'Clinique du Centre' },
];

export const facilityOptions: SelectOption[] = [
    { value: '', label: 'Aucun établissement' },
    { value: '9f881245-33ee-4b11-9a21-4f88e1478c99', label: 'Site principal' },
];

export const statusOptions: SelectOption[] = [
    { value: 'SCHEDULED', label: 'Planifié' },
    { value: 'PENDING', label: 'En attente' },
    { value: 'CONFIRMED', label: 'Confirmé' },
];
