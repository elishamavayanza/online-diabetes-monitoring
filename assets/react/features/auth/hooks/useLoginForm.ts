import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '@/react/app/providers/AuthProvider';
import { LoginFormValues } from '../types/auth.types';
import { ApiError, ApiErrorData } from '@/services/api/api.types';
import { SUSPENSION_CODES, SuspensionBlockInfo, SuspensionCode } from '@/react/features/security/types';

const initialValues: LoginFormValues = {
    emailOrUsername: '',
    password: '',
    rememberMe: false,
};

function buildSuspensionBlock(data: ApiErrorData): SuspensionBlockInfo | null {
    const code = data.code as SuspensionCode | undefined;
    if (!code || !SUSPENSION_CODES.includes(code)) return null;

    const payload = (data.data ?? {}) as Record<string, unknown>;
    const reason = typeof payload.reason === 'string' ? payload.reason : undefined;
    const endsAt = typeof payload.endsAt === 'string' ? payload.endsAt : undefined;

    return {
        code,
        title: code === 'organization_suspended'
            ? 'Organisation suspendue'
            : 'Compte suspendu',
        message: data.message
            ?? (code === 'organization_suspended'
                ? 'Votre organisation a été suspendue. Contactez le support pour plus d’informations.'
                : 'Votre compte a été suspendu. Contactez le support pour plus d’informations.'),
        reason,
        endsAt,
    };
}

export function useLoginForm() {
    const [values, setValues] = useState<LoginFormValues>(initialValues);
    const [errors, setErrors] = useState<Partial<LoginFormValues>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [submitError, setSubmitError] = useState<string | null>(null);
    const [block, setBlock] = useState<SuspensionBlockInfo | null>(null);
    const navigate = useNavigate();
    const { login } = useAuth();

    const handleChange =
        (field: keyof LoginFormValues) =>
            (e: React.ChangeEvent<HTMLInputElement>) => {
                setValues((prev) => ({ ...prev, [field]: e.target.value }));
                setErrors((prev) => ({ ...prev, [field]: undefined }));
                setSubmitError(null);
                setBlock(null);
            };

    const handleBooleanChange =
        (field: keyof LoginFormValues) =>
            (e: React.ChangeEvent<HTMLInputElement>) => {
                setValues((prev) => ({ ...prev, [field]: e.target.checked }));
            };

    const validate = (vals: LoginFormValues) => {
        const errs: Partial<LoginFormValues> = {};
        if (!vals.emailOrUsername.trim()) {
            errs.emailOrUsername = 'Veuillez saisir votre email ou nom d’utilisateur.';
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
            await login(values);
            console.log('Connexion réussie');
            const pendingInvite = sessionStorage.getItem('pendingInviteToken');
            if (pendingInvite) {
                sessionStorage.removeItem('pendingInviteToken');
                navigate(`/invite/${pendingInvite}`, { replace: true });
            } else {
                navigate('/app');
            }
        } catch (error: any) {
            const apiError = error as ApiError;
            const blockInfo = buildSuspensionBlock(apiError.data ?? {});
            if (blockInfo) {
                setBlock(blockInfo);
            } else {
                setSubmitError(error.message || 'Une erreur est survenue.');
            }
        } finally {
            setIsSubmitting(false);
        }
    };

    const resetBlock = () => {
        setBlock(null);
        setSubmitError(null);
    };

    return {
        values,
        errors,
        isSubmitting,
        submitError,
        block,
        resetBlock,
        handleChange,
        handleBooleanChange,
        handleSubmit,
    };
}
