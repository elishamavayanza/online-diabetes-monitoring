// ============================================================
// assets/services/security/security.utils.ts
// Utilitaires de sécurité (sanitisation, validation, chiffrement léger)
// ============================================================

// ─────────────────────────────────────────
// Sanitisation XSS
// ─────────────────────────────────────────

/**
 * Échappe les caractères HTML dangereux pour prévenir les attaques XSS.
 * À utiliser avant d'injecter du contenu dans le DOM via innerHTML.
 */
export function escapeHtml(raw: string): string {
    return raw
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/**
 * Supprime toutes les balises HTML d'une chaîne.
 */
export function stripHtml(html: string): string {
    const div = document.createElement('div');
    div.innerHTML = html;
    return div.textContent ?? div.innerText ?? '';
}

// ─────────────────────────────────────────
// Validation des données
// ─────────────────────────────────────────

/** Vérifie qu'une chaîne est une URL valide (http/https). */
export function isValidUrl(value: string): boolean {
    try {
        const url = new URL(value);
        return url.protocol === 'http:' || url.protocol === 'https:';
    } catch {
        return false;
    }
}

/** Vérifie qu'une URL est sur la même origine que l'application. */
export function isSameOrigin(url: string): boolean {
    try {
        return new URL(url, window.location.href).origin === window.location.origin;
    } catch {
        return false;
    }
}

/** Vérifie qu'une chaîne est une adresse e-mail valide. */
export function isValidEmail(email: string): boolean {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// ─────────────────────────────────────────
// Gestion des tokens JWT
// ─────────────────────────────────────────

export interface JwtPayload {
    sub?: string;
    exp?: number;
    iat?: number;
    roles?: string[];
    username?: string;
    fullName?: string;

    email?: string;
    permissions?: string[];
    role?: string;
    photoUrl?: string;
    avatarUrl?: string;
    [key: string]: unknown;
}

/**
 * Décode le payload d'un JWT sans vérifier la signature.
 * ⚠️ La vérification de signature doit toujours être faite côté serveur.
 */
export function decodeJwtPayload(token: string): JwtPayload | null {
    try {
        const base64 = token.split('.')[1];
        if (!base64) return null;
        // Padding Base64
        const padded = base64.replace(/-/g, '+').replace(/_/g, '/');
        const decoded = atob(padded.padEnd(padded.length + ((4 - (padded.length % 4)) % 4), '='));
        return JSON.parse(decoded) as JwtPayload;
    } catch {
        return null;
    }
}

/**
 * Vérifie si un token JWT est expiré (en se basant sur le claim `exp`).
 * @param bufferSeconds Marge de sécurité en secondes (défaut : 60)
 */
export function isTokenExpired(token: string, bufferSeconds = 60): boolean {
    const payload = decodeJwtPayload(token);
    if (!payload?.exp) return true;
    return Date.now() / 1000 >= payload.exp - bufferSeconds;
}

/**
 * Extrait les rôles Symfony depuis le payload JWT.
 */
export function extractRoles(token: string): string[] {
    const payload = decodeJwtPayload(token);
    return payload?.roles ?? [];
}

// ─────────────────────────────────────────
// Génération d'identifiants sécurisés
// ─────────────────────────────────────────

/**
 * Génère un UUID v4 aléatoire (crypto.randomUUID si disponible, sinon fallback).
 */
export function generateUUID(): string {
    if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
        return crypto.randomUUID();
    }
    // Fallback pour les navigateurs anciens
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
        const r = (Math.random() * 16) | 0;
        const v = c === 'x' ? r : (r & 0x3) | 0x8;
        return v.toString(16);
    });
}

/**
 * Génère une chaîne aléatoire sécurisée (pour nonces, états OAuth, etc.).
 * @param length Longueur en octets (défaut : 32)
 */
export function generateNonce(length = 32): string {
    const array = new Uint8Array(length);
    crypto.getRandomValues(array);
    return Array.from(array, (b) => b.toString(16).padStart(2, '0')).join('');
}

// ─────────────────────────────────────────
// Protection contre les attaques de timing
// ─────────────────────────────────────────

/**
 * Comparaison de chaînes en temps constant (prévient les timing attacks).
 * À utiliser pour comparer des tokens ou des hash côté client.
 */
export function timingSafeEqual(a: string, b: string): boolean {
    if (a.length !== b.length) return false;
    let result = 0;
    for (let i = 0; i < a.length; i++) {
        result |= a.charCodeAt(i) ^ b.charCodeAt(i);
    }
    return result === 0;
}

// ─────────────────────────────────────────
// Content-Security-Policy helpers
// ─────────────────────────────────────────

/**
 * Vérifie que le Content-Type d'une réponse est bien JSON.
 */
export function isJsonContentType(contentType: string | null): boolean {
    if (!contentType) return false;
    return contentType.includes('application/json') || contentType.includes('application/ld+json');
}

/**
 * Vérifie si un objet est une instance d'ApiError (duck typing).
 */
export function isApiError(error: unknown): error is { isApiError: true; status: number; message: string } {
    return (
        typeof error === 'object' &&
        error !== null &&
        'isApiError' in error &&
        (error as Record<string, unknown>).isApiError === true
    );
}
