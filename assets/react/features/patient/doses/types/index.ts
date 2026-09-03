export type IntakeStatus = 'TAKEN' | 'SKIPPED' | 'DELAYED' | 'PENDING';

export interface MedicationIntake {
    id: string;
    prescriptionItemId: string;
    time: string;
    medication: string;
    statut: IntakeStatus;
    takenAt?: string;
    quantityTaken?: string;
}

export interface DosesData {
    today: MedicationIntake[];
    markedDates?: { date: Date }[];

}
