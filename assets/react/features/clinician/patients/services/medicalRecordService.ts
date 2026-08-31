import { MedicalRecord } from '../types';

const mockRecords: Record<string, MedicalRecord> = {
    '1': {
        id: 'rec-001',
        patientId: '1',
        status: 'open',
        heightCm: 175,
        weightKg: 70,
        bloodType: 'A+',
        allergies: ['Pénicilline'],
        diagnoses: ['Diabète type 2'],
        createdAt: '2026-01-15T10:00:00Z',
        updatedAt: '2026-08-01T14:30:00Z',
    },
    '3': {
        id: 'rec-003',
        patientId: '3',
        status: 'closed',
        heightCm: 160,
        weightKg: 58,
        bloodType: 'O-',
        allergies: [],
        diagnoses: ['Diabète type 1'],
        createdAt: '2026-02-20T09:00:00Z',
        updatedAt: '2026-08-10T11:00:00Z',
    },
};

const delay = (ms: number) => new Promise((resolve) => setTimeout(resolve, ms));

export async function fetchMedicalRecord(patientId: string): Promise<MedicalRecord | null> {
    await delay(500);
    return mockRecords[patientId] ?? null;
}

export async function createMedicalRecord(patientId: string): Promise<MedicalRecord> {
    await delay(800);
    const newRecord: MedicalRecord = {
        id: `rec-${Date.now()}`,
        patientId,
        status: 'open',
        heightCm: undefined,
        weightKg: undefined,
        bloodType: undefined,
        allergies: [],
        diagnoses: [],
        createdAt: new Date().toISOString(),
        updatedAt: undefined, // ✅ au lieu de null
    };
    mockRecords[patientId] = newRecord;
    return newRecord;
}

export async function reopenMedicalRecord(patientId: string): Promise<MedicalRecord> {
    await delay(800);
    const existing = mockRecords[patientId];
    if (!existing) {
        throw new Error('Aucun dossier à rouvrir.');
    }
    const reopened: MedicalRecord = {
        ...existing,
        status: 'open',
        updatedAt: new Date().toISOString(),
    };
    mockRecords[patientId] = reopened;
    return reopened;
}
