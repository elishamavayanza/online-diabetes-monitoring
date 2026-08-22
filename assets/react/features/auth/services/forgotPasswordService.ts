import { ForgotPasswordFormValues, ForgotPasswordResponse } from '../types/forgotPassword.types';

/**
 * Simule l'envoi d'un email de réinitialisation.
 * Remplacez par un vrai appel API.
 */
export async function sendResetEmail(
    payload: ForgotPasswordFormValues
): Promise<ForgotPasswordResponse> {
    await new Promise((resolve) => setTimeout(resolve, 1500));

    if (payload.email.trim() === '') {
        throw new Error('Veuillez saisir un email valide.');
    }

    return {
        message: 'Si un compte existe avec cet email, un lien de réinitialisation a été envoyé.',
    };
}
