import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { AuthUser, LoginPayload } from '@/react/features/auth/types/auth.types';
import { login as loginService } from '@/react/features/auth/services/authService';
import { tokenStorage } from '@/services/storage/storage.service';
import { decodeJwtPayload, isTokenExpired } from '@/services/security/security.utils';
import { UserRole } from '@/react/app/layouts/MainLayout/components/Sidebar/sidebar.config';

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
    const [isLoading, setIsLoading] = useState(true);

    // Restaurer l'utilisateur depuis le JWT au chargement
    useEffect(() => {
        const token = tokenStorage.getAccessToken();
        if (token && !isTokenExpired(token)) {
            const payload = decodeJwtPayload(token);
            if (payload) {
                // Le rôle peut être une string ou undefined, on le normalise
                const role = (payload.role as UserRole) ?? 'PATIENT';
                const restoredUser: AuthUser = {
                    id: payload.sub ?? 'unknown',
                    name: payload.fullName ?? payload.username ?? 'Utilisateur',
                    email: payload.email ?? '',
                    permissions: payload.permissions ?? [],
                    role: role,
                    photoUrl: payload.photoUrl,
                };
                setUser(restoredUser);
            }
        } else {
            tokenStorage.clearAll();
        }
        setIsLoading(false);
    }, []);

    const login = async (payload: LoginPayload) => {
        setIsLoading(true);
        try {
            const response = await loginService(payload);
            setUser(response.user);
        } catch (error) {
            tokenStorage.clearAll();
            throw error;
        } finally {
            setIsLoading(false);
        }
    };

    const logout = () => {
        setUser(null);
        tokenStorage.clearAll();
    };

    const value = {
        user,
        isAuthenticated: !!user,
        isLoading,
        login,
        logout,
    };

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
}
