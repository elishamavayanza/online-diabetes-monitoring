import { MedicalRecordData } from '../types';

export async function fetchMedicalRecord(): Promise<MedicalRecordData> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return {
        personalInfo: {
            nom: 'Jean Dupont',
            dateNaissance: '1985-04-12',
            email: 'jean.dupont@example.com',
            telephone: '+243 990 111 222',
        },
        diabetesInfo: {
            type: 'Type 2',
            dateDiagnostic: '2020-03-15',
        },
        allergies: ['Pénicilline', 'Arachides'],
        emergencyContacts: [
            { nom: 'Marie Dupont', relation: 'Épouse', telephone: '+243 990 333 444' },
            { nom: 'Paul Dupont', relation: 'Frère', telephone: '+243 990 555 666' },
        ],
        diagnostics: [
            { id: 'd1', nom: 'Diabète de type 2', date: '2020-03-15' },
            { id: 'd2', nom: 'Hypertension légère', date: '2021-07-20' },
        ],
        notes: ['Patient suivi régulièrement.', 'Régime pauvre en sucres recommandé.'],
        consentements: [
            { id: 'c1', type: 'Partage des données avec professionnels', statut: 'Accepté' },
            { id: 'c2', type: 'Recherche clinique', statut: 'Refusé' },
        ],
    };
}
