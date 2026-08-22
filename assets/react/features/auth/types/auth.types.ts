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
}

export interface AuthResponse {
    token: string;
    user: AuthUser;
}

export interface LoginPayload {
    emailOrUsername: string;
    password: string;
}
