import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { AuthUser, LoginPayload } from '@/react/features/auth/types/auth.types';
import { login as loginService } from '@/react/features/auth/services/authService';
import { tokenStorage } from '@/services/storage/storage.service';
import { decodeJwtPayload, isTokenExpired } from '@/services/security/security.utils';
import { UserRole } from '@/react/app/layouts/MainLayout/components/Sidebar/sidebar.config';
import { resolveAvatarUrl } from '@/react/utils/avatarUrl';
import { fetchUserProfile } from '@/react/features/profile/services/profileService';

interface AuthContextValue {
    user: AuthUser | null;
    isAuthenticated: boolean;
    isLoading: boolean;
    login: (payload: LoginPayload) => Promise<void>;
    logout: () => void;
    updateUser: (changes: Partial<AuthUser>) => void;
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
    const [user, setUser] = useState<AuthUser | null>(null);
    const [isLoading, setIsLoading] = useState(true);

    // Restaurer l'utilisateur depuis le JWT puis récupérer le profil courant.
    // Le JWT peut avoir été émis avant le dernier changement de photo.
    useEffect(() => {
        let isMounted = true;

        const restoreSession = async () => {
            const token = tokenStorage.getAccessToken();
            if (token && !isTokenExpired(token)) {
                const payload = decodeJwtPayload(token);
                if (payload) {
                    // Le rôle peut être une string ou undefined, on le normalise
                    const role = (payload.role as UserRole) ?? 'PATIENT';
                    // Transformation sûre de la liste des organisations
                    const organizations = Array.isArray(payload.organizations)
                        ? payload.organizations
                        : [];

                    const restoredUser: AuthUser = {
                        id: payload.sub ?? 'unknown',
                        name: payload.fullName ?? payload.username ?? 'Utilisateur',
                        email: payload.email ?? '',
                        permissions: payload.permissions ?? [],
                        role: role,
                        photoUrl: resolveAvatarUrl(payload.photoUrl ?? payload.avatarUrl),
                        organizationId: organizations[0]?.organization_id,
                    };
                    setUser(restoredUser);

                    try {
                        const profile = await fetchUserProfile(restoredUser.id);
                        if (isMounted) {
                            setUser((currentUser) => currentUser && currentUser.id === restoredUser.id
                                ? { ...currentUser, name: profile.name || currentUser.name, photoUrl: resolveAvatarUrl(profile.avatarUrl) }
                                : currentUser
                            );
                        }
                    } catch {
                        // Le JWT reste utilisable si le profil ne peut pas être relu.
                    }
                }
            } else {
                tokenStorage.clearAll();
            }
            if (isMounted) setIsLoading(false);
        };

        void restoreSession();
        return () => {
            isMounted = false;
        };
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

    const updateUser = (changes: Partial<AuthUser>) => {
        setUser((currentUser) => currentUser ? { ...currentUser, ...changes } : currentUser);
    };

    const value = {
        user,
        isAuthenticated: !!user,
        isLoading,
        login,
        logout,
        updateUser,
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
