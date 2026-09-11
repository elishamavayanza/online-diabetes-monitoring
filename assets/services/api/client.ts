// ============================================================
// assets/services/api/client.ts
// Client HTTP basé sur Fetch — API Axios-like
// ============================================================

import type {
    RequestConfig,
    ApiResponse,
    ClientConfig,
    ApiErrorData,
    HttpMethod,
} from './api.types';
import { ApiError } from './api.types';
import {
    InterceptorManager,
    jwtInterceptor,
    csrfInterceptor,
    loggingRequestInterceptor,
    loggingResponseInterceptor,
    authErrorInterceptor,
    refreshAccessToken,
} from './interceptors';
import { isJsonContentType } from '../security/security.utils';

// ─────────────────────────────────────────
// Constantes
// ─────────────────────────────────────────

const DEFAULT_TIMEOUT    = 30_000; // 30 secondes
const DEFAULT_BASE_URL   = '/api';
const DEFAULT_RETRIES    = 2;
const DEFAULT_RETRY_DELAY = 300; // base du backoff exponentiel (ms)
const MAX_RETRY_DELAY    = 10_000; // plafond du backoff (ms)

// ─────────────────────────────────────────
// Classe principale
// ─────────────────────────────────────────

class HttpClient {
    private readonly config: Required<ClientConfig>;

    readonly interceptors = {
        request:  new InterceptorManager<RequestConfig>(),
        response: new InterceptorManager<ApiResponse>(),
    };

    constructor(config: ClientConfig) {
        this.config = {
            baseURL:        config.baseURL,
            timeout:        config.timeout        ?? DEFAULT_TIMEOUT,
            defaultHeaders: config.defaultHeaders ?? {},
            retries:        config.retries        ?? DEFAULT_RETRIES,
            retryDelay:     config.retryDelay     ?? DEFAULT_RETRY_DELAY,
        };

        // Enregistrement des intercepteurs par défaut
        this.interceptors.request.use(loggingRequestInterceptor.onFulfilled);
        this.interceptors.request.use(jwtInterceptor.onFulfilled);
        this.interceptors.request.use(csrfInterceptor.onFulfilled);

        this.interceptors.response.use(
            loggingResponseInterceptor.onFulfilled!,
            loggingResponseInterceptor.onRejected,
        );
        this.interceptors.response.use(
            authErrorInterceptor.onFulfilled!,
            authErrorInterceptor.onRejected,
        );
    }

    // ── Construction de l'URL ────────────────────

    private buildUrl(config: RequestConfig): string {
        let url = config.url.startsWith('http')
            ? config.url
            : `${this.config.baseURL}${config.url}`;

        if (config.params) {
            const qs = new URLSearchParams();
            Object.entries(config.params).forEach(([k, v]) => {
                if (v !== null && v !== undefined) {
                    qs.append(k, String(v));
                }
            });
            const queryString = qs.toString();
            if (queryString) url += (url.includes('?') ? '&' : '?') + queryString;
        }
        return url;
    }

    // ── Construction des headers ─────────────────

    private buildHeaders(config: RequestConfig): HeadersInit {
        const headers: Record<string, string> = {
            'Content-Type': 'application/json',
            Accept:         'application/json',
            ...this.config.defaultHeaders,
            ...config.headers,
        };
        return headers;
    }

    // ── Lecture de la réponse ────────────────────

    private async parseBody(response: Response): Promise<unknown> {
        const contentType = response.headers.get('Content-Type');
        if (isJsonContentType(contentType)) {
            try {
                return await response.json();
            } catch {
                return null;
            }
        }
        const text = await response.text();
        return text || null;
    }

    // ── Cœur de la requête ───────────────────────

    private async executeRequest<T>(config: RequestConfig, attempt = 0): Promise<ApiResponse<T>> {
        const url = this.buildUrl(config);

        // AbortController pour le timeout
        const controller = new AbortController();
        const signal = config.signal ?? controller.signal;
        const timeoutId = setTimeout(
            () => controller.abort(new DOMException('Request timeout', 'TimeoutError')),
            config.timeout ?? this.config.timeout,
        );

        try {
            const fetchOptions: RequestInit = {
                method:      config.method ?? 'GET',
                headers:     this.buildHeaders(config),
                credentials: config.credentials ?? 'same-origin',
                signal,
            };

            if (config.data !== undefined && config.method !== 'GET') {
                if (config.data instanceof FormData) {
                    fetchOptions.body = config.data;
                    // Supprimer Content-Type pour que le navigateur définisse la boundary
                    if (fetchOptions.headers && typeof fetchOptions.headers === 'object') {
                        delete (fetchOptions.headers as Record<string, string>)['Content-Type'];
                    }
                } else {
                    fetchOptions.body = JSON.stringify(config.data);
                }
            }

            const raw = await fetch(url, fetchOptions);
            clearTimeout(timeoutId);

            const body = await this.parseBody(raw);

            if (!raw.ok) {
                // Erreur HTTP : retry contrôlé (backoff exponentiel) si rejouable
                const apiError = new ApiError(
                    (body as ApiErrorData)?.message ?? raw.statusText,
                    raw.status,
                    raw.statusText,
                    (body as ApiErrorData) ?? {},
                    config,
                );
                if (this.isRetryableError(apiError) && attempt < this.maxRetries(config)) {
                    await this.delay(this.backoff(attempt, config.retryDelay ?? this.config.retryDelay));
                    return this.executeRequest<T>(config, attempt + 1);
                }
                this.throwOfflineIfNeeded(apiError, config);
                throw apiError;
            }

            return {
                data:       body as T,
                status:     raw.status,
                statusText: raw.statusText,
                headers:    raw.headers,
                config,
            };

        } catch (err) {
            clearTimeout(timeoutId);

            if (err instanceof ApiError) throw err;

            // Timeout explicite (n'attend pas la réponse du serveur)
            if (err instanceof DOMException && err.name === 'TimeoutError') {
                const timeoutError = new ApiError('La requête a expiré', 408, 'Request Timeout', {}, config);
                if (attempt < this.maxRetries(config)) {
                    await this.delay(this.backoff(attempt, config.retryDelay ?? this.config.retryDelay));
                    return this.executeRequest<T>(config, attempt + 1);
                }
                throw timeoutError;
            }
            if (err instanceof DOMException && err.name === 'AbortError') {
                throw new ApiError('Requête annulée', 0, 'Aborted', {}, config);
            }

            // Erreur réseau : retry uniquement si connecté (backoff exponentiel)
            const networkError = new ApiError(
                (err as Error)?.message ?? 'Erreur réseau inconnue',
                0,
                'Network Error',
                {},
                config,
            );
            if (this.isRetryableError(networkError) && attempt < this.maxRetries(config)) {
                await this.delay(this.backoff(attempt, config.retryDelay ?? this.config.retryDelay));
                return this.executeRequest<T>(config, attempt + 1);
            }
            this.throwOfflineIfNeeded(networkError, config);
            throw networkError;
        }
    }

    private delay(ms: number): Promise<void> {
        return new Promise((resolve) => setTimeout(resolve, ms));
    }

    // ── Helpers de retry ────────────────────────

    /**
     * Backoff exponentiel avec jitter (+/-50% du slot).
     * Ex. base 300 → ~300, ~600, ~1200, ... plafonné à 10s.
     */
    private backoff(attempt: number, base: number): number {
        const slot = Math.min(MAX_RETRY_DELAY, base * 2 ** attempt);
        return slot / 2 + Math.random() * slot;
    }

    /** Méthodes idempotentes : rejouées automatiquement sans risque. */
    private isIdempotent(method?: HttpMethod): boolean {
        return method === undefined || method === 'GET' || method === 'PUT' || method === 'DELETE';
    }

    /** Nombre maximal de tentatives pour une requête. */
    private maxRetries(config: RequestConfig): number {
        if (config.retries !== undefined) return config.retries;
        return this.isIdempotent(config.method) ? this.config.retries : 0;
    }

    /** Une erreur est-elle rejouable ? */
    private isRetryableError(error: ApiError): boolean {
        if (error.status >= 500) return true;          // erreur serveur temporaire
        if (error.status === 408) return true;         // timeout
        if (error.status === 429) return true;         // rate limit
        if (error.status === 0) return navigator.onLine !== false; // réseau : seulement si connecté
        return false;
    }

    /** Une erreur réseau est-elle un simple état hors-ligne ? */
    private throwOfflineIfNeeded(error: ApiError, config: RequestConfig): never {
        if (error.isOffline) {
            throw new ApiError(
                'Vous êtes hors-ligne. Vérifiez votre connexion.',
                0,
                'Offline',
                {},
                config,
            );
        }
        throw error;
    }

    // ── Méthode principale ───────────────────────

    async request<T = unknown>(config: RequestConfig): Promise<ApiResponse<T>> {
        // Pipeline requête
        const finalConfig = await this.interceptors.request.run(config);

        // Exécution
        let response: ApiResponse<T>;
        try {
            response = await this.executeRequest<T>(finalConfig);
        } catch (error) {
            const apiError = error as ApiError;
            if (apiError.isUnauthorized && !finalConfig.skipTokenRefresh && !finalConfig.hasRetriedAfterRefresh) {
                const newToken = await refreshAccessToken();
                if (newToken) {
                    return this.request<T>({ ...finalConfig, hasRetriedAfterRefresh: true });
                }
            }

            // Le refresh token est invalide/expiré : là seulement la session se termine.
            if (apiError.isUnauthorized) {
                authErrorInterceptor.onRejected?.(error);
            }
            throw error;
        }

        // Pipeline réponse (cast nécessaire pour le type générique)
        response = (await this.interceptors.response.run(response as ApiResponse)) as ApiResponse<T>;

        return response;
    }

    // ── Méthodes de commodité ────────────────────

    get<T = unknown>(url: string, config?: Omit<RequestConfig, 'url' | 'method'>): Promise<ApiResponse<T>> {
        return this.request<T>({ ...config, url, method: 'GET' });
    }

    post<T = unknown>(url: string, data?: unknown, config?: Omit<RequestConfig, 'url' | 'method' | 'data'>): Promise<ApiResponse<T>> {
        return this.request<T>({ ...config, url, method: 'POST', data });
    }

    put<T = unknown>(url: string, data?: unknown, config?: Omit<RequestConfig, 'url' | 'method' | 'data'>): Promise<ApiResponse<T>> {
        return this.request<T>({ ...config, url, method: 'PUT', data });
    }

    patch<T = unknown>(url: string, data?: unknown, config?: Omit<RequestConfig, 'url' | 'method' | 'data'>): Promise<ApiResponse<T>> {
        return this.request<T>({ ...config, url, method: 'PATCH', data });
    }

    delete<T = unknown>(url: string, config?: Omit<RequestConfig, 'url' | 'method'>): Promise<ApiResponse<T>> {
        return this.request<T>({ ...config, url, method: 'DELETE' });
    }
}

// ─────────────────────────────────────────
// Instance par défaut (singleton)
// ─────────────────────────────────────────

export const apiClient = new HttpClient({
    baseURL:  DEFAULT_BASE_URL,
    timeout:  DEFAULT_TIMEOUT,
    retries:  DEFAULT_RETRIES,
    defaultHeaders: {
        'X-Requested-With': 'XMLHttpRequest', // Symfony détecte les requêtes AJAX
    },
});

export { HttpClient };
export default apiClient;
