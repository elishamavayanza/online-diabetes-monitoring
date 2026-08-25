export interface MedicalRecordData {
    personalInfo: {
        nom: string;
        dateNaissance: string;
        email: string;
        telephone: string;
    };
    diabetesInfo: {
        type: string;
        dateDiagnostic: string;
    };
    allergies: string[];
    emergencyContacts: {
        nom: string;
        relation: string;
        telephone: string;
    }[];
    diagnostics: {
        id: string;
        nom: string;
        date: string;
    }[];
    notes: string[];
    consentements: {
        id: string;
        type: string;
        statut: 'Accepté' | 'Refusé';
    }[];
}
