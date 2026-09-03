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

// Détermine la période (Matin, Midi, Soir) à partir d'une heure "HH:mm"
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

function isSameDay(a: Date, b: Date): boolean {
    return (
        a.getFullYear() === b.getFullYear() &&
        a.getMonth() === b.getMonth() &&
        a.getDate() === b.getDate()
    );
}

export async function fetchDoses(date: Date = new Date()): Promise<DosesData> {
    const patientId = getCurrentUserIdFromToken();
    if (!patientId) throw new Error('Utilisateur non identifié.');

    const isToday = isSameDay(date, new Date());

    // 1. Récupérer toutes les prises enregistrées
    const intakeResponse = await apiClient.get<ApiFeedback<BackendIntake[]>>('/medication-intakes');
    const allRecordedIntakes = unwrapApiData(intakeResponse.data);

    // Calculer les dates marquées de manière fiable (sans conversion ISO)
    const markedDates = Array.from(
        new Set(
            allRecordedIntakes.map((intake) => {
                const d = new Date(intake.takenAt);
                return `${d.getFullYear()}-${d.getMonth()}-${d.getDate()}`;
            })
        )
    ).map((key) => {
        const [year, month, day] = key.split('-').map(Number);
        return { date: new Date(year, month, day) };
    });

    // Filtrer pour la date demandée
    const dateStart = new Date(date);
    dateStart.setHours(0, 0, 0, 0);
    const dateEnd = new Date(date);
    dateEnd.setHours(23, 59, 59, 999);

    const dateRecorded = allRecordedIntakes.filter((intake) => {
        const taken = new Date(intake.takenAt);
        return taken >= dateStart && taken <= dateEnd;
    });

    // Si ce n'est pas aujourd'hui, renvoyer uniquement les prises enregistrées
    if (!isToday) {
        const pastIntakes: MedicationIntake[] = dateRecorded.map((rec) => ({
            id: rec.id,
            prescriptionItemId: rec.prescriptionItemId,
            time: formatTime(rec.takenAt),
            medication: '', // sera rempli avec le nom via prescriptions si disponible
            statut: rec.status as IntakeStatus,
            takenAt: rec.takenAt,
            quantityTaken: rec.quantityTaken,
        }));

        pastIntakes.sort((a, b) => a.time.localeCompare(b.time));
        return { today: pastIntakes, markedDates };
    }

    // Aujourd'hui : générer les prises planifiées à partir des prescriptions actives
    const prescResponse = await apiClient.get<ApiFeedback<PrescriptionResponse[]>>(
        `/prescriptions/patient/${patientId}`
    );
    const prescriptions = unwrapApiData(prescResponse.data);
    const active = prescriptions.filter(
        (p) => p.status === 'ACTIVE' && (!p.endDate || new Date(p.endDate) > new Date())
    );

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
                const matchingRecorded = dateRecorded.find((rec) => {
                    const recPeriod = getPeriodFromTime(formatTime(rec.takenAt));
                    return rec.prescriptionItemId === item.id && recPeriod === period;
                });

                if (matchingRecorded) {
                    today.push({
                        id: matchingRecorded.id,
                        prescriptionItemId: item.id,
                        time: formatTime(matchingRecorded.takenAt),
                        medication: item.medicationName ?? 'Médicament',
                        statut: matchingRecorded.status as IntakeStatus,
                        takenAt: matchingRecorded.takenAt,
                        quantityTaken: matchingRecorded.quantityTaken,
                    });
                } else {
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

    today.sort((a, b) => a.time.localeCompare(b.time));
    return { today, markedDates };
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
