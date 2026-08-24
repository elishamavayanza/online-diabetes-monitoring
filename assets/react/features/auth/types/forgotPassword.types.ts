// Types pour le formulaire "Mot de passe oublié"
export interface ForgotPasswordFormValues {
    email: string;
}

// Réponse de l'API pour l'envoi de l'e-mail de réinitialisation
export interface ForgotPasswordResponse {
    message: string;
}

// Types pour le formulaire "Réinitialiser le mot de passe" (étape 2)
export interface ResetPasswordFormValues {
    token: string;
    newPassword: string;
    confirmPassword?: string; // optionnel, selon votre formulaire
}

// Réponse de l'API pour la réinitialisation effective
export interface ResetPasswordResponse {
    message: string;
}
