// ============================================================
// assets/services/api/interceptors.ts
// Pipeline d'intercepteurs request/response (pattern Axios-like)
// ============================================================

import type { RequestConfig, ApiResponse, RequestInterceptor, ResponseInterceptor } from './api.types';

// ─────────────────────────────────────────
// Gestionnaire d'intercepteurs générique
// ─────────────────────────────────────────

class InterceptorManager<T> {
    private handlers: Array<{ onFulfilled: (value: T) => T | Promise<T>; onRejected?: (error: unknown) => unknown } | null> = [];

    use(
        onFulfilled: (value: T) => T | Promise<T>,
        onRejected?: (error: unknown) => unknown,
    ): number {
        this.handlers.push({ onFulfilled, onRejected });
        return this.handlers.length - 1;
    }

    eject(id: number): void {
        if (this.handlers[id]) {
            this.handlers[id] = null;
        }
    }

    /** Applique tous les intercepteurs en séquence (pipeline). */
    async run(initialValue: T): Promise<T> {
        let value = initialValue;
        for (const handler of this.handlers) {
            if (!handler) continue;
            try {
                value = await handler.onFulfilled(value);
            } catch (error) {
                if (handler.onRejected) {
                    handler.onRejected(error);
                } else {
                    throw error;
                }
            }
        }
        return value;
    }

    forEach(fn: (handler: NonNullable<(typeof this.handlers)[number]>) => void): void {
        this.handlers.forEach((h) => {
            if (h !== null) fn(h);
        });
    }
}

// ─────────────────────────────────────────
// Intercepteurs de requête pré-configurés
// ─────────────────────────────────────────

import { getCsrfHeaders, requiresCsrf } from '../security/csrf';
import { tokenStorage } from '../storage/storage.service';
import { isTokenExpired } from '../security/security.utils';

let refreshInFlight: Promise<string | null> | null = null;

export async function refreshAccessToken(): Promise<string | null> {
    if (refreshInFlight) return refreshInFlight;

    const refreshToken = tokenStorage.getRefreshToken();
    if (!refreshToken) return null;

    refreshInFlight = fetch('/api/token/refresh', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({ refresh_token: refreshToken }),
        credentials: 'same-origin',
    })
        .then(async (response) => {
            if (!response.ok) return null;
            const data = await response.json() as { token?: string; refresh_token?: string };
            if (!data.token || !data.refresh_token) return null;
            tokenStorage.setAccessToken(data.token);
            tokenStorage.setRefreshToken(data.refresh_token);
            return data.token;
        })
        .catch(() => null)
        .finally(() => {
            refreshInFlight = null;
        });

    return refreshInFlight;
}

/**
 * Intercepteur : injecte le token JWT dans l'en-tête Authorization.
 */
export const jwtInterceptor: RequestInterceptor = {
    onFulfilled: async (config: RequestConfig): Promise<RequestConfig> => {
        if (config.skipTokenRefresh) return config;

        let token = tokenStorage.getAccessToken();
        // Rafraîchir une minute avant expiration évite toute interruption de travail.
        if (token && isTokenExpired(token, 60)) {
            token = await refreshAccessToken();
        }
        if (token) {
            return {
                ...config,
                headers: {
                    ...config.headers,
                    Authorization: `Bearer ${token}`,
                },
            };
        }
        return config;
    },
};

/**
 * Intercepteur : injecte le token CSRF pour les méthodes mutantes.
 */
export const csrfInterceptor: RequestInterceptor = {
    onFulfilled: (config: RequestConfig): RequestConfig => {
        if (config.skipCsrf) return config;
        const method = config.method ?? 'GET';
        if (!requiresCsrf(method)) return config;

        return {
            ...config,
            headers: {
                ...config.headers,
                ...getCsrfHeaders(),
            },
        };
    },
};

/**
 * Intercepteur : journalise les requêtes sortantes (dev uniquement).
 */
export const loggingRequestInterceptor: RequestInterceptor = {
    onFulfilled: (config: RequestConfig): RequestConfig => {
        if (process.env.NODE_ENV !== 'development') return config;
        console.groupCollapsed(`[API] → ${config.method ?? 'GET'} ${config.url}`);
        if (config.data) console.log('Body :', config.data);
        if (config.params) console.log('Params :', config.params);
        console.groupEnd();
        return config;
    },
};

// ─────────────────────────────────────────
// Intercepteurs de réponse pré-configurés
// ─────────────────────────────────────────

/**
 * Intercepteur : journalise les réponses reçues (dev uniquement).
 */
export const loggingResponseInterceptor: ResponseInterceptor = {
    onFulfilled: (response: ApiResponse): ApiResponse => {
        if (process.env.NODE_ENV !== 'development') return response;
        console.groupCollapsed(`[API] ← ${response.status} ${response.config.url}`);
        console.log('Data :', response.data);
        console.groupEnd();
        return response;
    },
    onRejected: (error: unknown): never => {
        if (process.env.NODE_ENV === 'development') {
            console.error('[API] Erreur :', error);
        }
        throw error;
    },
};

/**
 * Intercepteur : capture les erreurs 401 et déclenche la déconnexion.
 */
export const authErrorInterceptor: ResponseInterceptor = {
    onFulfilled: (response: ApiResponse) => response,
    onRejected: (error: unknown): never => {
        const apiError = error as { status?: number; isApiError?: boolean };
        if (apiError?.isApiError && apiError.status === 401) {
            // Nettoyage des tokens et redirection vers le login
            tokenStorage.clearAll();
            const loginUrl = document.querySelector<HTMLMetaElement>('meta[name="login-url"]')?.content ?? '/login';
            window.location.href = loginUrl;
        }
        throw error;
    },
};

// ─────────────────────────────────────────
// Export du gestionnaire
// ─────────────────────────────────────────

export { InterceptorManager };
export type { RequestInterceptor, ResponseInterceptor };
