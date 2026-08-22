import React from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { useAuth } from '../providers/AuthProvider';
import { LoginPage } from '@/react/features/auth/pages/LoginPage';
import { ForgotPasswordPage } from '@/react/features/auth/pages/ForgotPasswordPage'; // import ajouté
import AuthLayout from '../layouts/AuthLayout';
import AppLayout from '../layouts/AppLayout';
import HomePage from "@/react/homepage/HomePage/HomePage";

// Composant de route protégée
function ProtectedRoute({ children }: { children: React.ReactElement }) {
    const { isAuthenticated } = useAuth();
    return isAuthenticated ? children : <Navigate to="/login" replace />;
}

export default function AppRoutes() {
    return (
        <BrowserRouter>
            <Routes>
                {/* Page d'accueil publique */}
                <Route path="/" element={<HomePage />} />

                {/* Page de connexion publique */}
                <Route
                    path="/login"
                    element={
                        <AuthLayout>
                            <LoginPage />
                        </AuthLayout>
                    }
                />

                {/* Page mot de passe oublié – publique */}
                <Route
                    path="/forgot-password"
                    element={
                        <AuthLayout>
                            <ForgotPasswordPage />
                        </AuthLayout>
                    }
                />

                {/* Route protégée pour l'application */}
                <Route
                    path="/app"
                    element={
                        <ProtectedRoute>
                            <AppLayout>
                                <div>Dashboard (à venir)</div>
                            </AppLayout>
                        </ProtectedRoute>
                    }
                />

                {/* Redirection par défaut vers l'accueil */}
                <Route path="*" element={<Navigate to="/" replace />} />
            </Routes>
        </BrowserRouter>
    );
}
