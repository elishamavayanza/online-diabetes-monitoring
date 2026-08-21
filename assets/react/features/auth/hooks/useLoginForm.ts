import { useState } from 'react';
import { login } from '../services/authService';
import { LoginFormValues } from '../types/auth.types';

const initialValues: LoginFormValues = {
    emailOrUsername: '',
    password: '',
    rememberMe: false,
};

export function useLoginForm() {
    const [values, setValues] = useState<LoginFormValues>(initialValues);
    const [errors, setErrors] = useState<Partial<LoginFormValues>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [submitError, setSubmitError] = useState<string | null>(null);

    const handleChange =
        (field: keyof LoginFormValues) =>
            (e: React.ChangeEvent<HTMLInputElement>) => {
                setValues((prev) => ({ ...prev, [field]: e.target.value }));
                setErrors((prev) => ({ ...prev, [field]: undefined }));
                setSubmitError(null);
            };

    const handleBooleanChange =
        (field: keyof LoginFormValues) =>
            (e: React.ChangeEvent<HTMLInputElement>) => {
                setValues((prev) => ({ ...prev, [field]: e.target.checked }));
            };

    const validate = (vals: LoginFormValues) => {
        const errs: Partial<LoginFormValues> = {};
        if (!vals.emailOrUsername.trim()) {
            errs.emailOrUsername = 'Veuillez saisir votre  ou nom d’utilisateur.';
        }
        if (!vals.password) {
            errs.password = 'Veuillez saisir votre mot de passe.';
        }
        return errs;
    };

    const handleSubmit = async (e?: React.FormEvent) => {
        e?.preventDefault();
        setSubmitError(null);

        const validationErrors = validate(values);
        setErrors(validationErrors);
        if (Object.keys(validationErrors).length > 0) return;

        setIsSubmitting(true);
        try {
            const response = await login(values);
            // Stocker le token / rediriger selon votre stratégie
            console.log('Connexion réussie', response);
            // Exemple : localStorage.setItem('token', response.token);
        } catch (error: any) {
            setSubmitError(error.message || 'Une erreur est survenue.');
        } finally {
            setIsSubmitting(false);
        }
    };

    return {
        values,
        errors,
        isSubmitting,
        submitError,
        handleChange,
        handleBooleanChange,
        handleSubmit,
    };
}
