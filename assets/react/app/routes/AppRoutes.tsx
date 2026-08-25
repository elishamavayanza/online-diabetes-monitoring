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
import {ClinicianDashboardPage} from "@/react/features/clinician/dashboard/pages/ClinicianDashboardPage";
import {ClinicianPatientsPage} from "@/react/features/clinician/patients/pages/ClinicianPatientsPage";
import {AgendaPage} from "@/react/features/clinician/agenda/pages/AgendaPage";
import { AppointmentPage } from '@/react/features/clinician/appointments/pages/AppointmentPage';
import { MessagesPage } from '@/react/features/clinician/messages/pages/MessagesPage';
import { ClinicianNotificationsPage } from '@/react/features/clinician/notifications/pages/ClinicianNotificationsPage';
import { NutritionistDashboardPage } from '@/react/features/nutritionist/dashboard/pages/NutritionistDashboardPage';
import { NutritionistPatientsPage } from '@/react/features/nutritionist/patients/pages/NutritionistPatientsPage';
import { MealPlansPage } from '@/react/features/nutritionist/plans/pages/MealPlansPage';
import { FoodsPage } from '@/react/features/nutritionist/foods/pages/FoodsPage';
import {NotificationPages} from "@/react/features/nutritionist/notifications/pages/NotificationPages";
import {MessagesPages} from "@/react/features/nutritionist/messages/pages/MessagesPages";
import {AppointmentsPages} from "@/react/features/nutritionist/appointments/pages/AppointmentsPages";
import {AgendaPages} from "@/react/features/nutritionist/agenda/pages/AgendaPages";



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

                <Route
                    path="/clinician"
                    element={
                        <ProtectedRoute>
                            <MainLayout />
                        </ProtectedRoute>
                    }
                >
                    <Route index element={<Navigate to="/clinician/dashboard" replace />} />
                    <Route path="dashboard" element={<ClinicianDashboardPage />} />
                    <Route path="my-patients" element={<ClinicianPatientsPage />} />
                    <Route path="agenda" element={<AgendaPage />} />
                    <Route path="appointments" element={<AppointmentPage />} />
                    <Route path="messages" element={<MessagesPage />} />
                    <Route path="notifications" element={<ClinicianNotificationsPage />} />
                </Route>

                <Route
                    path="/nutritionist"
                    element={
                        <ProtectedRoute>
                            <MainLayout />
                        </ProtectedRoute>
                    }
                >
                    <Route index element={<Navigate to="/nutritionist/dashboard" replace />} />
                    <Route path="dashboard" element={<NutritionistDashboardPage />} />
                    <Route path="my-patients" element={<NutritionistPatientsPage />} />
                    <Route path="plans" element={<MealPlansPage />} />
                    <Route path="foods" element={<FoodsPage />} />
                    <Route path="agenda" element={<AgendaPages />} />
                    <Route path="appointments" element={<AppointmentsPages />} />
                    <Route path="messages" element={<MessagesPages />} />
                    <Route path="notifications" element={<NotificationPages />} />
                </Route>

                {/* Fallback pour /app, au cas où */}
                <Route path="/app" element={<Navigate to="/root/dashboard" replace />} />

                <Route path="*" element={<Navigate to="/" replace />} />
            </Routes>
        </BrowserRouter>
    );
}
