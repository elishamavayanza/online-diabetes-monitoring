// services/medicalRecordService.ts
import { fetchPatientDossier } from '@/react/features/clinician/patients/services/patientDossierService';
import type {
    PatientAllergy,
    PatientDossierData,
    PatientMedicalNote,
} from '@/react/features/clinician/patients/types';
import {
    MedicalRecordData,
    AllergyInfo,
    DiagnosisInfo,
    MedicalNoteInfo,
    ConsentInfo,
    VitalsInfo,
    RecordEvent,
} from '../types';

function noteDate(note: PatientMedicalNote): string {
    return note.notedAt ?? note.createdAt;
}

function sortNotesByDate(notes: PatientMedicalNote[]): MedicalNoteInfo[] {
    return [...notes]
        .sort(
            (a, b) => new Date(noteDate(b)).getTime() - new Date(noteDate(a)).getTime(),
        )
        .map((n) => ({
            id: n.id,
            content: n.content,
            date: noteDate(n),
            authorName: n.authorName,
        }));
}

function buildDiabetesInfo(dossier: PatientDossierData) {
    const diabetesDiagnosis = dossier.diagnoses.find((d) =>
        d.conditionName.toLowerCase().includes('diab'),
    );

    return {
        type: diabetesDiagnosis?.conditionName ?? 'Non renseigné',
        dateDiagnostic: diabetesDiagnosis?.diagnosedAt ?? '',
    };
}

function buildAllergies(dossier: PatientDossierData): AllergyInfo[] {
    return dossier.allergies.map((a: PatientAllergy) => ({
        id: a.id,
        name: a.name,
        severity: a.severity,
        reaction: a.reaction,
        notes: a.notes,
    }));
}

function buildDiagnostics(dossier: PatientDossierData): DiagnosisInfo[] {
    return dossier.diagnoses.map((d) => ({
        id: d.id,
        nom: d.conditionName,
        date: d.diagnosedAt ?? '',
        description: d.description,
        status: d.status,
    }));
}

function buildConsents(dossier: PatientDossierData): ConsentInfo[] {
    return dossier.consents.map((c) => ({
        id: c.id,
        type: c.consentType ?? 'Consentement',
        statut: c.revokedAt ? 'Refusé' : 'Accepté',
        date: c.revokedAt ?? c.grantedAt,
    }));
}

function buildVitals(dossier: PatientDossierData): VitalsInfo {
    const glucose = dossier.measurements.bloodGlucose[0];
    const bp = dossier.measurements.bloodPressure[0];
    const weight = dossier.measurements.weight[0];
    const hba1c = dossier.measurements.hba1c[0];

    return {
        glucose: glucose ? { value: glucose.value, unit: glucose.unit, date: glucose.createdAt } : undefined,
        bloodPressure: bp ? { systolic: bp.systolic, diastolic: bp.diastolic, date: bp.createdAt } : undefined,
        weight: weight ? { valueKg: weight.valueKg, date: weight.createdAt } : undefined,
        hba1c: hba1c ? { valuePercent: hba1c.valuePercent, date: hba1c.createdAt } : undefined,
    };
}

function buildEvents(dossier: PatientDossierData): RecordEvent[] {
    const events: RecordEvent[] = [];

    dossier.notes.forEach((n) => {
        events.push({
            id: `note-${n.id}`,
            kind: 'note',
            date: noteDate(n),
            label: 'Note médicale',
            meta: n.authorName,
        });
    });

    dossier.appointments.forEach((a) => {
        events.push({
            id: `appt-${a.id}`,
            kind: 'appointment',
            date: a.scheduledAt,
            label: a.reason || 'Consultation',
            meta: a.professionalName || 'Professionnel de santé',
            status: a.status,
        });
    });

    const m = dossier.measurements;
    m.bloodGlucose.forEach((g) => events.push({
        id: `msg-${g.id}`, kind: 'measurement', date: g.createdAt,
        label: 'Glycémie', meta: `${g.value} ${g.unit ?? 'mg/dL'}`,
    }));
    m.bloodPressure.forEach((bp) => events.push({
        id: `mbp-${bp.id}`, kind: 'measurement', date: bp.createdAt,
        label: 'Tension artérielle', meta: `${bp.systolic}/${bp.diastolic} mmHg`,
    }));
    m.weight.forEach((w) => events.push({
        id: `mw-${w.id}`, kind: 'measurement', date: w.createdAt,
        label: 'Poids', meta: `${w.valueKg} kg`,
    }));
    m.hba1c.forEach((h) => events.push({
        id: `mh-${h.id}`, kind: 'measurement', date: h.createdAt,
        label: 'HbA1c', meta: `${Number(h.valuePercent).toFixed(1)} %`,
    }));

    return events.sort(
        (a, b) => new Date(b.date).getTime() - new Date(a.date).getTime(),
    );
}

// Transforme PatientDossierData en MedicalRecordData
function mapDossierToMedicalRecord(dossier: PatientDossierData): MedicalRecordData {
    return {
        personalInfo: {
            nom: dossier.profile.fullName,
            dateNaissance: dossier.profile.dateOfBirth ?? '',
            email: dossier.profile.email,
            telephone: dossier.profile.phone ?? '',
            bloodType: dossier.profile.bloodType,
            heightCm: dossier.profile.heightCm,
        },
        diabetesInfo: buildDiabetesInfo(dossier),
        allergies: buildAllergies(dossier),
        emergencyContacts: dossier.emergencyContacts.map((c) => ({
            nom: c.fullName,
            relation: c.relationship ?? '',
            telephone: c.phone ?? '',
        })),
        diagnostics: buildDiagnostics(dossier),
        notes: sortNotesByDate(dossier.notes),
        consentements: buildConsents(dossier),
        vitals: buildVitals(dossier),
        events: buildEvents(dossier),
        hasRecord: dossier.record !== null,
    };
}

export async function fetchMedicalRecord(patientId: string): Promise<MedicalRecordData> {
    const dossier = await fetchPatientDossier(patientId);
    return mapDossierToMedicalRecord(dossier);
}