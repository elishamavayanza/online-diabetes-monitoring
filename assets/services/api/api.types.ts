// ============================================================
// assets/services/api/api.types.ts
// Types & interfaces partagés de la couche HTTP
// ============================================================

// ─────────────────────────────────────────
// Méthodes HTTP supportées
// ─────────────────────────────────────────
export type HttpMethod = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';

// ─────────────────────────────────────────
// Configuration d'une requête
// ─────────────────────────────────────────
export interface RequestConfig {
    /** URL relative ou absolue */
    url: string;
    method?: HttpMethod;
    /** Corps de la requête (sérialisé automatiquement en JSON) */
    data?: unknown;
    /** Query params ajoutés à l'URL */
    params?: Record<string, string | number | boolean | null | undefined>;
    /** Headers supplémentaires */
    headers?: Record<string, string>;
    /** Timeout en millisecondes (défaut : 30 000) */
    timeout?: number;
    /** Envoyer les credentials (cookies) – défaut : 'same-origin' */
    credentials?: RequestCredentials;
    /** Signal d'annulation (AbortController) */
    signal?: AbortSignal;
    /** Désactive l'ajout automatique du token CSRF */
    skipCsrf?: boolean;
    /** Désactive le rafraîchissement automatique du token */
    skipTokenRefresh?: boolean;
}

// ─────────────────────────────────────────
// Réponse normalisée
// ─────────────────────────────────────────
export interface ApiResponse<T = unknown> {
    data: T;
    status: number;
    statusText: string;
    headers: Headers;
    /** Config de la requête originale */
    config: RequestConfig;
}

// ─────────────────────────────────────────
// Erreur API normalisée
// ─────────────────────────────────────────
export interface ApiErrorData {
    message?: string;
    errors?: Record<string, string[]>;
    code?: string | number;
    [key: string]: unknown;
}

export class ApiError extends Error {
    public readonly status: number;
    public readonly statusText: string;
    public readonly data: ApiErrorData;
    public readonly config: RequestConfig;
    public readonly isApiError = true as const;

    constructor(
        message: string,
        status: number,
        statusText: string,
        data: ApiErrorData,
        config: RequestConfig,
    ) {
        super(message);
        this.name = 'ApiError';
        this.status = status;
        this.statusText = statusText;
        this.data = data;
        this.config = config;
    }

    /** Vérifie si l'erreur est liée à l'authentification (401) */
    get isUnauthorized(): boolean {
        return this.status === 401;
    }

    /** Vérifie si l'erreur est une erreur de validation (422) */
    get isValidation(): boolean {
        return this.status === 422;
    }

    /** Vérifie si l'erreur est une erreur serveur (5xx) */
    get isServerError(): boolean {
        return this.status >= 500;
    }
}

// ─────────────────────────────────────────
// Interceptors (request / response)
// ─────────────────────────────────────────
export interface RequestInterceptor {
    onFulfilled: (config: RequestConfig) => RequestConfig | Promise<RequestConfig>;
    onRejected?: (error: unknown) => unknown;
}

export interface ResponseInterceptor<T = unknown> {
    onFulfilled: (response: ApiResponse<T>) => ApiResponse<T> | Promise<ApiResponse<T>>;
    onRejected?: (error: unknown) => unknown;
}

// ─────────────────────────────────────────
// Configuration du client HTTP
// ─────────────────────────────────────────
export interface ClientConfig {
    /** URL de base (ex: "/api") */
    baseURL: string;
    /** Timeout global en ms */
    timeout?: number;
    /** Headers par défaut */
    defaultHeaders?: Record<string, string>;
    /** Nombre de tentatives automatiques sur erreur réseau */
    retries?: number;
}

// ─────────────────────────────────────────
// Options de pagination
// ─────────────────────────────────────────
export interface PaginationParams {
    page?: number;
    limit?: number;
    sort?: string;
    order?: 'asc' | 'desc';
}

export interface PaginatedResponse<T> {
    data: T[];
    total: number;
    page: number;
    limit: number;
    totalPages: number;
}
