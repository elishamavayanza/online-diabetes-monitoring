export interface PersonalInfo {
    nom: string;
    dateNaissance: string;
    email: string;
    telephone: string;
    bloodType?: string;
    heightCm?: number;
}

export interface DiabetesInfo {
    type: string;
    dateDiagnostic: string;
}

export interface AllergyInfo {
    id: string;
    name: string;
    severity?: string;
    reaction?: string;
    notes?: string;
}

export interface EmergencyContactInfo {
    nom: string;
    relation: string;
    telephone: string;
}

export interface DiagnosisInfo {
    id: string;
    nom: string;
    date: string;
    description?: string;
    status?: string;
}

export interface MedicalNoteInfo {
    id: string;
    content: string;
    date: string;
    authorName?: string;
}

export interface ConsentInfo {
    id: string;
    type: string;
    statut: 'Accepté' | 'Refusé';
    date: string;
}

export interface VitalsInfo {
    glucose?: { value: number; unit?: string; date: string };
    bloodPressure?: { systolic: number; diastolic: number; date: string };
    weight?: { valueKg: number; date: string };
    hba1c?: { valuePercent: number; date: string };
}

export type RecordEventKind = 'note' | 'appointment' | 'measurement';

export interface RecordEvent {
    id: string;
    kind: RecordEventKind;
    date: string;
    label: string;
    meta?: string;
    status?: string;
}

export interface MedicalRecordData {
    personalInfo: PersonalInfo;
    diabetesInfo: DiabetesInfo;
    allergies: AllergyInfo[];
    emergencyContacts: EmergencyContactInfo[];
    diagnostics: DiagnosisInfo[];
    notes: MedicalNoteInfo[];
    consentements: ConsentInfo[];
    vitals: VitalsInfo;
    events: RecordEvent[];
    hasRecord: boolean;
}