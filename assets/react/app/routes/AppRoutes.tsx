import React from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { useAuth } from '../providers/AuthProvider';
import { LoginPage } from '@/react/features/auth/pages/LoginPage';
import { ForgotPasswordPage } from '@/react/features/auth/pages/ForgotPasswordPage';
import HomePage from "@/react/homepage/HomePage/HomePage";
import AuthLayout from "@/react/app/layouts/AuthLayout/AuthLayout";
import { DashboardPage as RootDashboardPage } from '@/react/features/root/dashboard/pages/DashboardPage';
import { MainLayout } from "@/react/app/layouts/MainLayout/MainLayout";
import { OrganisationsPage } from '@/react/features/root/organisations/pages/OrganisationsPage';


function ProtectedRoute({ children }: { children: React.ReactElement }) {
    const { isAuthenticated } = useAuth();
    return isAuthenticated ? children : <Navigate to="/login" replace />;
}

function PublicRoute({ children }: { children: React.ReactElement }) {
    const { isAuthenticated, user } = useAuth();

    if (isAuthenticated && user) {
        let destination = '/root/dashboard'; // fallback par défaut
        switch (user.role) {
            case 'ROOT':
                destination = '/root/dashboard';
                break;
            case 'ADMIN':
                destination = '/admin/dashboard';
                break;
            case 'CLINICIAN':
                destination = '/clinician/dashboard';
                break;
            case 'NUTRITIONIST':
                destination = '/nutritionist/dashboard';
                break;
            case 'PATIENT':
                destination = '/patient/summary';
                break;
            default:
                destination = '/root/dashboard'; // pour éviter /app inexistant
        }
        return <Navigate to={destination} replace />;
    }

    return children;
}

export default function AppRoutes() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/" element={<HomePage />} />

                <Route
                    path="/login"
                    element={
                        <PublicRoute>
                            <AuthLayout>
                                <LoginPage />
                            </AuthLayout>
                        </PublicRoute>
                    }
                />

                <Route
                    path="/forgot-password"
                    element={
                        <PublicRoute>
                            <AuthLayout>
                                <ForgotPasswordPage />
                            </AuthLayout>
                        </PublicRoute>
                    }
                />

                {/* Routes protégées avec MainLayout */}
                <Route
                    path="/root"
                    element={
                        <ProtectedRoute>
                            <MainLayout />
                        </ProtectedRoute>
                    }
                >
                    <Route index element={<Navigate to="/root/dashboard" replace />} />
                    <Route path="dashboard" element={<RootDashboardPage />} />
                    <Route path="organisations" element={<OrganisationsPage />} />

                </Route>

                {/* Fallback pour /app, au cas où */}
                <Route path="/app" element={<Navigate to="/root/dashboard" replace />} />

                <Route path="*" element={<Navigate to="/" replace />} />
            </Routes>
        </BrowserRouter>
    );
}
