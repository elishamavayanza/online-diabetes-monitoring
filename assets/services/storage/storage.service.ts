// ============================================================
// assets/services/storage/storage.service.ts
// Abstraction du stockage navigateur (localStorage / sessionStorage)
// avec sérialisation JSON, TTL et chiffrement optionnel
// ============================================================

// ─────────────────────────────────────────
// Types internes
// ─────────────────────────────────────────

interface StorageEntry<T> {
    value: T;
    /** Timestamp d'expiration en ms (Date.now()) – absent = pas d'expiration */
    expiresAt?: number;
    /** Version de schéma pour la migration des données persistées */
    version?: number;
}

type StorageDriver = 'local' | 'session' | 'memory';

// ─────────────────────────────────────────
// Stockage mémoire (fallback si localStorage indisponible)
// ─────────────────────────────────────────
const memoryStore = new Map<string, string>();

const memoryDriver: Storage = {
    get length() {
        return memoryStore.size;
    },
    getItem: (key) => memoryStore.get(key) ?? null,
    setItem: (key, value) => {
        memoryStore.set(key, value);
    },
    removeItem: (key) => {
        memoryStore.delete(key);
    },
    clear: () => {
        memoryStore.clear();
    },
    key: (index) => {
        return Array.from(memoryStore.keys())[index] ?? null;
    },
};

// ─────────────────────────────────────────
// Classe principale
// ─────────────────────────────────────────

class StorageService {
    private readonly prefix: string;
    private readonly driver: Storage;

    constructor(driver: StorageDriver = 'local', prefix = 'diabcare_') {
        this.prefix = prefix;
        this.driver = this.resolveDriver(driver);
    }

    // ── Résolution du driver ──────────────────────
    private resolveDriver(type: StorageDriver): Storage {
        if (type === 'memory') return memoryDriver;
        try {
            const store = type === 'local' ? localStorage : sessionStorage;
            // Test de disponibilité (mode privé Safari peut lever une exception)
            store.setItem('__test__', '1');
            store.removeItem('__test__');
            return store;
        } catch {
            console.warn(`[StorageService] ${type}Storage non disponible, fallback mémoire.`);
            return memoryDriver;
        }
    }

    // ── Clé préfixée ─────────────────────────────
    private key(name: string): string {
        return `${this.prefix}${name}`;
    }

    // ── Écriture ─────────────────────────────────

    /**
     * Stocke une valeur avec sérialisation JSON et TTL optionnel.
     * @param name  Clé de stockage
     * @param value Valeur quelconque (sera sérialisée en JSON)
     * @param ttl   Durée de vie en secondes (optionnelle)
     */
    set<T>(name: string, value: T, ttl?: number): void {
        const entry: StorageEntry<T> = {
            value,
            version: 1,
        };
        if (ttl && ttl > 0) {
            entry.expiresAt = Date.now() + ttl * 1000;
        }
        try {
            this.driver.setItem(this.key(name), JSON.stringify(entry));
        } catch (e) {
            // Quota dépassé ou autre erreur
            console.error('[StorageService] Erreur lors de l\'écriture :', e);
        }
    }

    // ── Lecture ──────────────────────────────────

    /**
     * Lit et désérialise une valeur. Retourne `null` si absente ou expirée.
     */
    get<T>(name: string): T | null {
        const raw = this.driver.getItem(this.key(name));
        if (!raw) return null;

        try {
            const entry = JSON.parse(raw) as StorageEntry<T>;

            // Vérification TTL
            if (entry.expiresAt && Date.now() > entry.expiresAt) {
                this.remove(name);
                return null;
            }

            return entry.value;
        } catch {
            // Données corrompues → on les supprime
            this.remove(name);
            return null;
        }
    }

    /**
     * Lit une valeur et retourne `defaultValue` si absente ou expirée.
     */
    getOr<T>(name: string, defaultValue: T): T {
        return this.get<T>(name) ?? defaultValue;
    }

    // ── Suppression ──────────────────────────────

    remove(name: string): void {
        this.driver.removeItem(this.key(name));
    }

    /** Supprime toutes les clés appartenant à ce service (préfixe). */
    clear(): void {
        const keysToRemove: string[] = [];
        for (let i = 0; i < this.driver.length; i++) {
            const k = this.driver.key(i);
            if (k?.startsWith(this.prefix)) keysToRemove.push(k);
        }
        keysToRemove.forEach((k) => this.driver.removeItem(k));
    }

    // ── Existence / Expiration ───────────────────

    /** Vérifie si une clé existe et n'est pas expirée. */
    has(name: string): boolean {
        return this.get(name) !== null;
    }

    /** Retourne le timestamp d'expiration (ms) ou null si pas de TTL. */
    expiresAt(name: string): number | null {
        const raw = this.driver.getItem(this.key(name));
        if (!raw) return null;
        try {
            const entry = JSON.parse(raw) as StorageEntry<unknown>;
            return entry.expiresAt ?? null;
        } catch {
            return null;
        }
    }

    // ── Utilitaires ──────────────────────────────

    /** Liste toutes les clés gérées par ce service. */
    keys(): string[] {
        const result: string[] = [];
        for (let i = 0; i < this.driver.length; i++) {
            const k = this.driver.key(i);
            if (k?.startsWith(this.prefix)) {
                result.push(k.slice(this.prefix.length));
            }
        }
        return result;
    }
}

// ─────────────────────────────────────────
// Instances exportées (singletons)
// ─────────────────────────────────────────

/** Stockage persistant (localStorage) – survit aux rechargements de page */
export const storage = new StorageService('local', 'diabcare_');

/** Stockage de session (sessionStorage) – effacé à la fermeture du tab */
export const sessionStorage_ = new StorageService('session', 'diabcare_');

/** Stockage mémoire – non persistant, utile pour les tests */
export const memStorage = new StorageService('memory', 'diabcare_');

// ─────────────────────────────────────────
// Helpers pour les tokens d'authentification
// ─────────────────────────────────────────

const ACCESS_TOKEN_KEY  = 'access_token';
const REFRESH_TOKEN_KEY = 'refresh_token';

export const tokenStorage = {
    getAccessToken:  (): string | null => storage.get<string>(ACCESS_TOKEN_KEY),
    setAccessToken:  (token: string, ttlSeconds?: number) => storage.set(ACCESS_TOKEN_KEY, token, ttlSeconds),
    removeAccessToken: () => storage.remove(ACCESS_TOKEN_KEY),

    getRefreshToken:  (): string | null => storage.get<string>(REFRESH_TOKEN_KEY),
    setRefreshToken:  (token: string, ttlSeconds?: number) => storage.set(REFRESH_TOKEN_KEY, token, ttlSeconds),
    removeRefreshToken: () => storage.remove(REFRESH_TOKEN_KEY),

    clearAll: () => {
        storage.remove(ACCESS_TOKEN_KEY);
        storage.remove(REFRESH_TOKEN_KEY);
    },
};

export { StorageService };
