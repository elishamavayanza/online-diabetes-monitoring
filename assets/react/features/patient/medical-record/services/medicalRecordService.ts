// services/medicalRecordService.ts
import { fetchPatientDossier } from '@/react/features/clinician/patients/services/patientDossierService';
import type {
    PatientAllergy, PatientDiagnosis,
    PatientDossierData,
    PatientEmergencyContact, PatientMedicalConsent, PatientMedicalNote
} from '@/react/features/clinician/patients/types';
import {MedicalRecordData} from "@/react/features/patient/medical-record/types";

// Transforme PatientDossierData en MedicalRecordData
function mapDossierToMedicalRecord(dossier: PatientDossierData): MedicalRecordData {
    return {
        personalInfo: {
            nom: dossier.profile.fullName,
            dateNaissance: dossier.profile.dateOfBirth ?? '—',
            email: dossier.profile.email,
            telephone: dossier.profile.phone ?? '—',
        },
        diabetesInfo: {
            type: 'Type 2', // à ajuster si le backend fournit cette info
            dateDiagnostic: '', // à remplir si disponible
        },
        allergies: dossier.allergies.map((a: PatientAllergy) => a.name),
        emergencyContacts: dossier.emergencyContacts.map(
            (c: PatientEmergencyContact) => ({
                nom: c.fullName,
                relation: c.relationship ?? '',
                telephone: c.phone ?? '',
            })
        ),
        diagnostics: dossier.diagnoses.map((d: PatientDiagnosis) => ({
            id: d.id,
            nom: d.conditionName,
            date: d.diagnosedAt ?? '',
        })),
        notes: dossier.notes.map((n: PatientMedicalNote) => n.content),
        consentements: dossier.consents.map((c: PatientMedicalConsent) => ({
            id: c.id,
            type: c.consentType ?? 'Consentement',
            statut: c.revokedAt ? 'Refusé' : 'Accepté',
        })),
    };
}

export async function fetchMedicalRecord(patientId: string): Promise<MedicalRecordData> {
    const dossier = await fetchPatientDossier(patientId);
    return mapDossierToMedicalRecord(dossier);
}
