import { useClinicianPatients } from '@/react/features/clinician/patients/hooks/useClinicianPatients';

export function useNutritionistPatients() {
    return useClinicianPatients();
}
