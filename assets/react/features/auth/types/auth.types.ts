export interface LoginFormValues {
    emailOrUsername: string;
    password: string;
}

export interface AuthUser {
    id: string;
    name: string;
    email: string;
}

export interface AuthResponse {
    token: string;
    user: AuthUser;
}

export interface LoginPayload {
    emailOrUsername: string;
    password: string;
}
