// services/treatmentsService.ts
import apiClient from '@/services/api/client';
import { ApiFeedback, unwrapApiData } from '@/react/utils/apiFeedback';
import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';
import { Treatment, TreatmentCategory, TreatmentsData } from '../types';

interface PrescriptionResponse {
    id: string;
    status: string;
    startDate?: string;
    endDate?: string;
    prescriber?: {
        fullName?: string;
    } | null;
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

// Nouvelle interface pour la réponse d'un médicament
interface MedicationResponse {
    id: string;
    name: string;
    categorie?: string;
    category?: string;
}

function buildHoraires(morning: boolean, noon: boolean, evening: boolean): string[] {
    const horaires: string[] = [];
    if (morning) horaires.push('Matin');
    if (noon) horaires.push('Midi');
    if (evening) horaires.push('Soir');
    return horaires.length > 0 ? horaires : ['Selon prescription'];
}

// Fonction de secours si la catégorie n'est pas disponible
function fallbackCategory(name: string, dosage: string): TreatmentCategory {
    const lower = (name + ' ' + dosage).toLowerCase();
    if (lower.includes('insuline') || lower.includes('unité')) return 'INSULINE';
    if (lower.includes('comprimé') || lower.includes('comprime') || lower.includes('mg')) return 'COMPRIMÉ';
    return 'AUTRE';
}

// Convertit la catégorie brute du backend en type frontend
function mapCategory(category?: string): TreatmentCategory | null {
    if (!category) return null;
    const normalized = category
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');

    if (normalized === 'insulin') return 'INSULINE';
    if (normalized === 'tablet') return 'COMPRIMÉ';
    if (normalized === 'other') return 'AUTRE';
    // fallback pour d'éventuelles anciennes valeurs
    if (normalized === 'comprime' || normalized === 'comprimé') return 'COMPRIMÉ';
    return null;
}

// Récupère le nom et la catégorie d'un médicament via son ID
async function fetchMedicationInfo(medicationId: string): Promise<{
    name: string;
    category: TreatmentCategory | null;
}> {
    try {
        const response = await apiClient.get<ApiFeedback<MedicationResponse>>(
            `/medications/${medicationId}`
        );
        const med = unwrapApiData(response.data, 'Erreur médicament');
        const rawCategory = med.categorie ?? med.category;
        return {
            name: med.name || 'Médicament',
            category: mapCategory(rawCategory),
        };
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

        // Déterminer si la prescription est active
        const isActive =
            prescription.status === 'ACTIVE' &&
            (!prescription.endDate || new Date(prescription.endDate) > new Date());

        for (const item of items) {
            const medicationInfo = await fetchMedicationInfo(item.medicationId);
            const category = medicationInfo.category ?? fallbackCategory(
                item.medicationName ?? medicationInfo.name,
                item.dosage
            );

            const treatment: Treatment = {
                id: item.id,
                categorie: category,
                nom: item.medicationName ?? medicationInfo.name,
                dosage: item.dosage || item.quantity || 'Dosage inconnu',
                horaires: buildHoraires(item.morning, item.noon, item.evening),
                instructions: item.instructions,
                quantity: item.quantity,
                startDate: prescription.startDate,
                endDate: prescription.endDate,
                prescriberName: prescription.prescriber?.fullName ?? '',
            };

            // Ajouter à la liste appropriée
            if (isActive) {
                treatments.push(treatment);
            } else {
                pastTreatments.push(treatment);
            }
        }
    }

    return { treatments, pastTreatments };
}

