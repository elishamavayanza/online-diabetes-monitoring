import apiClient from '@/services/api/client';
import { ApiFeedback, unwrapApiData } from '@/react/utils/apiFeedback';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { Treatment, TreatmentCategory, TreatmentsData } from '../types';

interface PrescriptionResponse {
    id: string;
    status: string;
    startDate?: string;
    endDate?: string;
    prescriber?: { fullName?: string } | null;
    stopReason?: string;
    notes?: string;
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
    instructions?: string;
}

interface MedicationResponse {
    id: string;
    name: string;
    category?: string;
    categorie?: string;
}

function buildHoraires(morning: boolean, noon: boolean, evening: boolean): string[] {
    const horaires: string[] = [];
    if (morning) horaires.push('Matin');
    if (noon) horaires.push('Midi');
    if (evening) horaires.push('Soir');
    return horaires.length > 0 ? horaires : ['Selon prescription'];
}

function fallbackCategory(name: string, dosage: string): TreatmentCategory {
    const lower = (name + ' ' + dosage).toLowerCase();
    if (lower.includes('insuline') || lower.includes('unité')) return 'INSULINE';
    if (lower.includes('comprimé') || lower.includes('comprime') || lower.includes('mg')) return 'COMPRIMÉ';
    return 'AUTRE';
}

function mapCategory(category?: string): TreatmentCategory | null {
    if (!category) return null;
    const normalized = category.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    if (normalized === 'insulin') return 'INSULINE';
    if (normalized === 'tablet') return 'COMPRIMÉ';
    if (normalized === 'other') return 'AUTRE';
    if (normalized === 'comprime' || normalized === 'comprimé') return 'COMPRIMÉ';
    return null;
}

async function fetchMedicationInfo(medicationId: string): Promise<{ name: string; category: TreatmentCategory | null }> {
    try {
        const response = await apiClient.get<ApiFeedback<MedicationResponse>>(`/medications/${medicationId}`);
        const med = unwrapApiData(response.data, 'Erreur médicament');
        const rawCategory = med.categorie ?? med.category;
        return { name: med.name || 'Médicament', category: mapCategory(rawCategory) };
    } catch {
        return { name: 'Médicament', category: null };
    }
}

export async function fetchTreatments(): Promise<TreatmentsData> {
    const patientId = getCurrentUserIdFromToken();
    if (!patientId) throw new Error('Utilisateur non identifié.');

    const prescResponse = await apiClient.get<ApiFeedback<PrescriptionResponse[]>>(
        `/prescriptions/patient/${patientId}`
    );
    const prescriptions = unwrapApiData(prescResponse.data, 'Erreur lors du chargement des prescriptions.');

    const treatments: Treatment[] = [];
    const pastTreatments: Treatment[] = [];

    for (const prescription of prescriptions) {
        const itemsResponse = await apiClient.get<ApiFeedback<PrescriptionItemResponse[]>>(
            `/prescription-items/prescription/${prescription.id}`
        );
        const items = unwrapApiData(itemsResponse.data, 'Erreur lors du chargement des médicaments.');

        const isActive = prescription.status === 'ACTIVE' &&
            (!prescription.endDate || new Date(prescription.endDate) > new Date());

        for (const item of items) {
            const medicationInfo = await fetchMedicationInfo(item.medicationId);
            const category = medicationInfo.category ?? fallbackCategory(item.medicationName ?? medicationInfo.name, item.dosage);

            const treatment: Treatment = {
                id: item.id,
                prescriptionId: prescription.id,
                categorie: category,
                nom: item.medicationName ?? medicationInfo.name,
                dosage: item.dosage || item.quantity || 'Dosage inconnu',
                horaires: buildHoraires(item.morning, item.noon, item.evening),
                instructions: item.instructions,
                quantity: item.quantity,
                startDate: prescription.startDate,
                endDate: prescription.endDate,
                prescriberName: prescription.prescriber?.fullName ?? '',
                stopReason: prescription.notes,
                status: prescription.status,
            };

            if (isActive) {
                treatments.push(treatment);
            } else {
                pastTreatments.push(treatment);
            }
        }
    }

    return { treatments, pastTreatments };
}

export async function stopTreatment(prescriptionId: string, reason?: string): Promise<void> {
    const response = await apiClient.patch<ApiFeedback<unknown>>(
        `/prescriptions/${prescriptionId}/stop`,
        { reason: reason || null }
    );
    unwrapApiData(response.data, "Erreur lors de l'arrêt du traitement.");
}
