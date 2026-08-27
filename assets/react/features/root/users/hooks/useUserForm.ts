import { useState } from 'react';
import { useCreateProfessional } from './useCreateProfessional';
import { useUpdateProfessional } from './useUpdateProfessional';
import { useCreatePatient } from './useCreatePatient';
import { useUpdatePatient } from './useUpdatePatient';
import { UserFormType, ProfessionalFormValues, PatientFormValues } from '../types/userForm.types';

export function useUserForm(mode: 'create' | 'edit', initialUser?: { type: UserFormType; data: any }) {
    const [userType, setUserType] = useState<UserFormType>(initialUser?.type ?? 'patient');
    const [step, setStep] = useState(mode === 'edit' ? 1 : 0); //  commence à l'étape 1 en édition

    const createProfessional = useCreateProfessional();
    const updateProfessional = useUpdateProfessional(initialUser?.data as ProfessionalFormValues);
    const createPatient = useCreatePatient();
    const updatePatient = useUpdatePatient(initialUser?.data as PatientFormValues);

    const isProfessional = userType === 'professional';
    const isPatient = userType === 'patient';

    const handleSubmit = async () => {
        if (mode === 'create') {
            if (isProfessional) await createProfessional.submit();
            else await createPatient.submit();
        } else {
            if (isProfessional) await updateProfessional.submit(initialUser!.data.id);
            else await updatePatient.submit(initialUser!.data.id);
        }
    };

    return {
        userType,
        setUserType,
        step,
        setStep,
        isProfessional,
        isPatient,
        createProfessional,
        updateProfessional,
        createPatient,
        updatePatient,
        handleSubmit,
    };
}
