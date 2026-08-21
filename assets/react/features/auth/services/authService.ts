// @ts-ignore
import { AuthResponse, LoginPayload } from '../types/auth.types';

/**
 * Simule une requête d'authentification.
 * Remplacez la simulation par un appel API réel.
 */
export async function login(payload: LoginPayload): Promise<AuthResponse> {
    // Simule une latence réseau
    await new Promise((resolve) => setTimeout(resolve, 1500));

    // Exemple de validation factice
    if (payload.emailOrUsername === 'admin' && payload.password === 'admin') {
        return {
            token: 'fake-token-123',
            user: {
                id: '1',
                name: 'Admin DiabCare',
                email: 'admin@diabcare.com',
            },
        };
    }

    throw new Error('Identifiants incorrects. Veuillez réessayer.');
}

export interface LoginPayload {
    emailOrUsername: string;
    password: string;
    rememberMe: boolean;
}
