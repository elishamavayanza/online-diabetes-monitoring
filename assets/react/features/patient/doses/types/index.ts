export type IntakeStatus = 'TAKEN' | 'SKIPPED' | 'DELAYED' | 'PENDING';

export interface MedicationIntake {
    id: string;
    time: string;
    prescriptionItemId: string;
    medication: string;
    statut: IntakeStatus;
}

export interface DosesData {
    today: MedicationIntake[];
}
