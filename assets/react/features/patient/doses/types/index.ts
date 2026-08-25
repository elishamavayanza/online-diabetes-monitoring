export type IntakeStatus = 'TAKEN' | 'SKIPPED' | 'DELAYED' | 'PENDING';

export interface MedicationIntake {
    id: string;
    time: string;
    medication: string;
    statut: IntakeStatus;
}

export interface DosesData {
    today: MedicationIntake[];
}
