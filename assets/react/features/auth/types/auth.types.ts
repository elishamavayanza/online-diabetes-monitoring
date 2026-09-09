import {UserRole} from "@/react/app/layouts/MainLayout/components/Sidebar/sidebar.config";

export interface LoginFormValues {
    emailOrUsername: string;
    password: string;
    rememberMe: boolean;
}

export interface AuthUser {
    id: string;
    name: string;
    email: string;
    permissions: string[];

    role: UserRole;
    /** URL de la photo de profil (optionnel) */
    photoUrl?: string;
    organizationId?: string;
    /** Langue de l'interface choisie par l'utilisateur (ex. 'fr', 'en') */
    locale?: string;
}

export interface AuthResponse {
    token: string;
    user: AuthUser;
}

export interface LoginPayload {
    emailOrUsername: string;
    password: string;
}
