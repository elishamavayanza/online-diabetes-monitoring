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
import { UsersPage } from '@/react/features/root/users/pages/UsersPage';
import { RolesPage } from '@/react/features/root/roles/pages/RolesPage';
import { NotificationsPage } from '@/react/features/root/notifications/pages/NotificationsPage';
import { AuditPage } from '@/react/features/root/audit/pages/AuditPage';
import { SettingsPage } from '@/react/features/root/settings/pages/SettingsPage';
import { AdminDashboardPage } from '@/react/features/admin/dashboard/pages/AdminDashboardPage';
import { EstablishmentsPage } from '@/react/features/admin/establishments/pages/EstablishmentsPage';
import { DepartmentsPage } from '@/react/features/admin/departments/pages/DepartmentsPage';
import { ProfessionalsPage } from '@/react/features/admin/professionals/pages/ProfessionalsPage';
import { MembersPage } from '@/react/features/admin/members/pages/MembersPage';
import { PatientsPage } from '@/react/features/admin/patients/pages/PatientsPage';
import { AppointmentsPage } from '@/react/features/admin/appointments/pages/AppointmentsPage';
import { ActivityPage } from '@/react/features/admin/activity/pages/ActivityPage';
import { AdminNotificationsPage } from '@/react/features/admin/notifications/pages/AdminNotificationsPage';
import { AdminSettingsPage } from '@/react/features/admin/settings/pages/AdminSettingsPage';

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
                    <Route path="users" element={<UsersPage />} />
                    <Route path="roles" element={<RolesPage />} />
                    <Route path="notifications" element={<NotificationsPage />} />
                    <Route path="audit" element={<AuditPage />} />
                    <Route path="settings" element={<SettingsPage />} />
                </Route>

                <Route
                    path="/admin"
                    element={
                        <ProtectedRoute>
                            <MainLayout />
                        </ProtectedRoute>
                     }
                >
                    <Route index element={<Navigate to="/admin/dashboard" replace />} />
                    <Route path="dashboard" element={<AdminDashboardPage />} />
                    <Route path="establishments" element={<EstablishmentsPage />} />
                    <Route path="departments" element={<DepartmentsPage />} />
                    <Route path="professionals" element={<ProfessionalsPage />} />
                    <Route path="members" element={<MembersPage />} />
                    <Route path="patients" element={<PatientsPage />} />
                    <Route path="appointments" element={<AppointmentsPage />} />
                    <Route path="activity" element={<ActivityPage />} />
                    <Route path="notifications" element={<AdminNotificationsPage />} />
                    <Route path="settings" element={<AdminSettingsPage />} />
                </Route>

                {/* Fallback pour /app, au cas où */}
                <Route path="/app" element={<Navigate to="/root/dashboard" replace />} />

                <Route path="*" element={<Navigate to="/" replace />} />
            </Routes>
        </BrowserRouter>
    );
}
