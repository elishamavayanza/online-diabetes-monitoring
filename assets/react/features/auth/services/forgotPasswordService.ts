import { apiClient } from '@/services/api/client';
import type {
    ForgotPasswordFormValues,
    ForgotPasswordResponse,
    ResetPasswordFormValues,
    ResetPasswordResponse,
} from '@/react/features/auth';

/**
 * Envoie une demande de réinitialisation de mot de passe à l'API.
 * Endpoint : POST /api/forgot-password
 */
export async function sendResetEmail(
    payload: ForgotPasswordFormValues
): Promise<ForgotPasswordResponse> {
    const response = await apiClient.post<ForgotPasswordResponse>(
        '/forgot-password',
        payload
    );
    return response.data;
}

/**
 * Réinitialise le mot de passe avec le token reçu par e-mail.
 * Endpoint : POST /api/reset-password
 */
export async function resetPasswordWithToken(
    payload: ResetPasswordFormValues
): Promise<ResetPasswordResponse> {
    const response = await apiClient.post<ResetPasswordResponse>(
        '/reset-password',
        payload
    );
    return response.data;
}
