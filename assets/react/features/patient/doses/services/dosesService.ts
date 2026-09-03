// services/dosesService.ts
import apiClient from '@/services/api/client';
import { ApiFeedback, unwrapApiData } from '@/react/utils/apiFeedback';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { DosesData, IntakeStatus, MedicationIntake } from '../types';

interface PrescriptionResponse {
    id: string;
    status: string;
    startDate?: string;
    endDate?: string;
}

interface PrescriptionItemResponse {
    id: string;
    prescriptionId: string;
    medicationId: string;
    medicationName?: string;
    dosage: string;
    quantity: string;
    morning: boolean;
    noon: boolean;
    evening: boolean;
}

interface BackendIntake {
    id: string;
    prescriptionItemId: string;
    takenAt: string;
    status: string;
    quantityTaken?: string;
}

// Détermine la période à partir d'une heure (HH:mm)
function getPeriodFromTime(time: string): string {
    const hour = parseInt(time.split(':')[0], 10);
    if (hour < 12) return 'Matin';
    if (hour < 15) return 'Midi';
    return 'Soir';
}

// Convertit une date ISO en "HH:mm" locale
function formatTime(dateStr: string): string {
    return new Date(dateStr).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
}

export async function fetchDoses(): Promise<DosesData> {
    const patientId = getCurrentUserIdFromToken();
    if (!patientId) throw new Error('Utilisateur non identifié.');

    // 1. Récupérer les prescriptions actives
    const prescResponse = await apiClient.get<ApiFeedback<PrescriptionResponse[]>>(
        `/prescriptions/patient/${patientId}`
    );
    const prescriptions = unwrapApiData(prescResponse.data);
    const active = prescriptions.filter(
        (p) => p.status === 'ACTIVE' && (!p.endDate || new Date(p.endDate) > new Date())
    );

    // 2. Récupérer toutes les prises enregistrées aujourd'hui
    const intakeResponse = await apiClient.get<ApiFeedback<BackendIntake[]>>('/medication-intakes');
    const recordedIntakes = unwrapApiData(intakeResponse.data);
    const now = new Date();
    const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const todayEnd = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);

    const todayRecorded = recordedIntakes.filter((intake) => {
        const taken = new Date(intake.takenAt);
        return taken >= todayStart && taken < todayEnd;
    });

    // 3. Construire la liste finale
    const today: MedicationIntake[] = [];

    for (const prescription of active) {
        const itemsResponse = await apiClient.get<ApiFeedback<PrescriptionItemResponse[]>>(
            `/prescription-items/prescription/${prescription.id}`
        );
        const items = unwrapApiData(itemsResponse.data);

        for (const item of items) {
            const periods: string[] = [];
            if (item.morning) periods.push('Matin');
            if (item.noon) periods.push('Midi');
            if (item.evening) periods.push('Soir');

            periods.forEach((period) => {
                // Chercher une prise enregistrée pour cet item et cette période
                const matchingRecorded = todayRecorded.find((rec) => {
                    const recPeriod = getPeriodFromTime(formatTime(rec.takenAt));
                    return rec.prescriptionItemId === item.id && recPeriod === period;
                });

                if (matchingRecorded) {
                    today.push({
                        id: matchingRecorded.id,
                        prescriptionItemId: item.id,
                        time: formatTime(matchingRecorded.takenAt), // heure réelle
                        medication: item.medicationName ?? 'Médicament',
                        statut: matchingRecorded.status as IntakeStatus,
                        takenAt: matchingRecorded.takenAt,
                        quantityTaken: matchingRecorded.quantityTaken,
                    });
                } else {
                    // Prise planifiée non enregistrée
                    today.push({
                        id: `planned-${item.id}-${period}`,
                        prescriptionItemId: item.id,
                        time: period === 'Matin' ? '08:00' : period === 'Midi' ? '12:00' : '18:00',
                        medication: item.medicationName ?? 'Médicament',
                        statut: 'PENDING',
                    });
                }
            });
        }
    }

    // 4. Trier par heure
    today.sort((a, b) => a.time.localeCompare(b.time));

    return { today };
}

export async function recordIntake(data: {
    prescriptionItemId: string;
    takenAt: string;
    quantityTaken: string;
    status: IntakeStatus;
}): Promise<void> {
    const response = await apiClient.post<ApiFeedback<unknown>>('/medication-intakes', data);
    unwrapApiData(response.data, "Erreur lors de l'enregistrement de la prise.");
}
