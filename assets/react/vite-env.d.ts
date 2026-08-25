/// <reference types="vite/client" />

interface ImportEnv {
    VITE_API_BASE_URL: string;
    // Ajoutez d'autres variables si nécessaire :
    // VITE_ANOTHER_VARIABLE?: string;
}

interface ImportMeta {
    readonly env: ImportEnv;
}
