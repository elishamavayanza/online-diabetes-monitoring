import React, { createContext, useContext, useState, ReactNode } from 'react';
import { AuthUser, LoginPayload } from '@/react/features/auth/types/auth.types';
import { login as loginService } from '@/react/features/auth/services/authService';

interface AuthContextValue {
    user: AuthUser | null;
    isAuthenticated: boolean;
    isLoading: boolean;
    login: (payload: LoginPayload) => Promise<void>;
    logout: () => void;
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
    const [user, setUser] = useState<AuthUser | null>(null);
    const [isLoading, setIsLoading] = useState(false);

    const login = async (payload: LoginPayload) => {
        setIsLoading(true);
        try {
            const response = await loginService(payload);
            setUser(response.user);
            // Stocker le token si besoin
            localStorage.setItem('token', response.token);
        } finally {
            setIsLoading(false);
        }
    };

    const logout = () => {
        setUser(null);
        localStorage.removeItem('token');
    };

    const value: AuthContextValue = {
        user,
        isAuthenticated: !!user,
        isLoading,
        login,
        logout,
    };

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

// Hook personnalisé pour utiliser le contexte
export function useAuth(): AuthContextValue {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
}
