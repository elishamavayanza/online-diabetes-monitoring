import { ClinicianPatient } from '../types';

export async function fetchClinicianPatients(search: string): Promise<ClinicianPatient[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    const all: ClinicianPatient[] = [
        {
            id: '1',
            nom: 'Jean Dupont',
            derniereConsultation: '2026-08-20',
            prochainRendezVous: '2026-08-28 09:00',
            statut: 'Active',
            medicalRecordStatus: 'open', // ✅ dossier ouvert
        },
        {
            id: '2',
            nom: 'Marie Zawadi',
            derniereConsultation: '2026-08-18',
            prochainRendezVous: '2026-08-26 10:30',
            statut: 'Active',
            medicalRecordStatus: 'none', // ✅ pas encore de dossier
        },
        {
            id: '3',
            nom: 'Paul K.',
            derniereConsultation: '2026-08-10',
            prochainRendezVous: '—',
            statut: 'Inactive',
            medicalRecordStatus: 'closed', // ✅ dossier fermé
        },
    ];

    if (!search) return all;
    return all.filter((p) => p.nom.toLowerCase().includes(search.toLowerCase()));
}
