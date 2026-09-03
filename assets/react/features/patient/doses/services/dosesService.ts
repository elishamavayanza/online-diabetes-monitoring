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
}

function horairesToTimes(morning: boolean, noon: boolean, evening: boolean): string[] {
    const times: string[] = [];
    if (morning) times.push('08:00');
    if (noon) times.push('12:00');
    if (evening) times.push('18:00');
    return times;
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

    const plannedIntakes: MedicationIntake[] = [];

    // 2. Pour chaque prescription active, récupérer les items et générer les prises planifiées
    for (const prescription of active) {
        const itemsResponse = await apiClient.get<ApiFeedback<PrescriptionItemResponse[]>>(
            `/prescription-items/prescription/${prescription.id}`
        );
        const items = unwrapApiData(itemsResponse.data);

        for (const item of items) {
            const times = horairesToTimes(item.morning, item.noon, item.evening);
            times.forEach((time) => {
                plannedIntakes.push({
                    id: `planned-${item.id}-${time}`,
                    prescriptionItemId: item.id,
                    time,
                    medication: item.medicationName ?? 'Médicament',
                    statut: 'PENDING',
                });
            });
        }
    }

    // 3. Récupérer les prises déjà enregistrées aujourd'hui
    const intakeResponse = await apiClient.get<ApiFeedback<BackendIntake[]>>('/medication-intakes');
    const recordedIntakes = unwrapApiData(intakeResponse.data);
    const now = new Date();
    const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const todayEnd = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);

    const todayRecorded = recordedIntakes
        .filter((intake) => {
            const taken = new Date(intake.takenAt);
            return taken >= todayStart && taken < todayEnd;
        })
        .map((intake) => ({
            id: intake.id,
            prescriptionItemId: intake.prescriptionItemId,
            time: new Date(intake.takenAt).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
            medication: '', // sera remplacé
            statut: intake.status as IntakeStatus,
        }));

    // 4. Fusionner : remplacer les prises planifiées par les enregistrées si même item et même heure
    const today: MedicationIntake[] = plannedIntakes.map((planned) => {
        const found = todayRecorded.find(
            (rec) => rec.prescriptionItemId === planned.prescriptionItemId && rec.time === planned.time
        );
        if (found) {
            return {
                ...planned,
                id: found.id,
                statut: found.statut,
                medication: planned.medication,
            };
        }
        return planned;
    });

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
