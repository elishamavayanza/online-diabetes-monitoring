import { useState } from 'react';
import { sendResetEmail } from '@/react/features/auth';
import { ForgotPasswordFormValues } from '@/react/features/auth';

const initialValues: ForgotPasswordFormValues = {
    email: '',
};

export function useForgotPasswordForm() {
    const [values, setValues] = useState<ForgotPasswordFormValues>(initialValues);
    const [errors, setErrors] = useState<Partial<ForgotPasswordFormValues>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [submitError, setSubmitError] = useState<string | null>(null);
    const [successMessage, setSuccessMessage] = useState<string | null>(null);

    const handleChange =
        (field: keyof ForgotPasswordFormValues) =>
            (e: React.ChangeEvent<HTMLInputElement>) => {
                setValues((prev) => ({ ...prev, [field]: e.target.value }));
                setErrors((prev) => ({ ...prev, [field]: undefined }));
                setSubmitError(null);
                setSuccessMessage(null);
            };

    const validate = (vals: ForgotPasswordFormValues) => {
        const errs: Partial<ForgotPasswordFormValues> = {};
        if (!vals.email.trim()) {
            errs.email = 'Veuillez saisir votre adresse email.';
        } else if (!/\S+@\S+\.\S+/.test(vals.email)) {
            errs.email = 'Format d’email invalide.';
        }
        return errs;
    };

    const handleSubmit = async (e?: React.FormEvent) => {
        e?.preventDefault();
        setSubmitError(null);
        setSuccessMessage(null);

        const validationErrors = validate(values);
        setErrors(validationErrors);
        if (Object.keys(validationErrors).length > 0) return;

        setIsSubmitting(true);
        try {
            const response = await sendResetEmail(values);
            setSuccessMessage(response.message);
            // Optionnel : on vide le champ après succès
            setValues({ email: '' });
        } catch (error: any) {
            // L'erreur est une ApiError avec éventuellement data.message
            const message =
                error?.data?.message ||
                error?.message ||
                'Une erreur est survenue.';
            setSubmitError(message);
        } finally {
            setIsSubmitting(false);
        }
    };

    return {
        values,
        errors,
        isSubmitting,
        submitError,
        successMessage,
        handleChange,
        handleSubmit,
    };
}
