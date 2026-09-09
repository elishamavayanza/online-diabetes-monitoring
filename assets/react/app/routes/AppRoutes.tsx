import React, { lazy, Suspense } from 'react';
import { BrowserRouter, Routes, Route, Navigate, useLocation } from 'react-router-dom';
import { ErrorBoundary } from '@/react/components/ErrorBoundary';
import { useAuth } from '../providers/AuthProvider';
import { LoginPage } from '@/react/features/auth/pages/LoginPage';
import { ForgotPasswordPage } from '@/react/features/auth/pages/ForgotPasswordPage';
import HomePage from "@/react/homepage/HomePage/HomePage";
import AuthLayout from "@/react/app/layouts/AuthLayout/AuthLayout";
import { Loading } from '@/react/components/UI/Loading';

// ─────────────────────────────────────────
// Lazy loading : chaque page n'est chargée
// qu'à la première navigation vers sa route.
// `lazyNamed` gère les exports nommés.
// ─────────────────────────────────────────

type ComponentType<T = unknown> = React.ComponentType<T>;

function lazyNamed(loader: () => Promise<Record<string, unknown>>, name: string) {
    return lazy(() =>
        loader().then((mod) => ({
            default: mod[name] as ComponentType,
        })),
    );
}

const MainLayout = lazy(() => import("@/react/app/layouts/MainLayout/MainLayout").then((m) => ({ default: m.MainLayout })));

const RootDashboardPage = lazyNamed(() => import('@/react/features/root/dashboard/pages/DashboardPage'), 'DashboardPage');
const OrganisationsPage = lazyNamed(() => import('@/react/features/root/organisations/pages/OrganisationsPage'), 'OrganisationsPage');
const UsersPage = lazyNamed(() => import('@/react/features/root/users/pages/UsersPage'), 'UsersPage');
const RolesPage = lazyNamed(() => import('@/react/features/root/roles/pages/RolesPage'), 'RolesPage');
const NotificationsPage = lazyNamed(() => import('@/react/features/root/notifications/pages/NotificationsPage'), 'NotificationsPage');
const AuditPage = lazyNamed(() => import('@/react/features/root/audit/pages/AuditPage'), 'AuditPage');
const SettingsPage = lazyNamed(() => import('@/react/features/root/settings/pages/SettingsPage'), 'SettingsPage');

const AdminDashboardPage = lazyNamed(() => import('@/react/features/admin/dashboard/pages/AdminDashboardPage'), 'AdminDashboardPage');
const DepartmentsPage = lazyNamed(() => import('@/react/features/admin/departments/pages/DepartmentsPage'), 'DepartmentsPage');
const ProfessionalsPage = lazyNamed(() => import('@/react/features/admin/professionals/pages/ProfessionalsPage'), 'ProfessionalsPage');
const MembersPage = lazyNamed(() => import('@/react/features/admin/members/pages/MembersPage'), 'MembersPage');
const PatientsPage = lazyNamed(() => import('@/react/features/admin/patients/pages/PatientsPage'), 'PatientsPage');
const AppointmentsPage = lazyNamed(() => import('@/react/features/admin/appointments/pages/AppointmentsPage'), 'AppointmentsPage');
const ActivityPage = lazyNamed(() => import('@/react/features/admin/activity/pages/ActivityPage'), 'ActivityPage');
const AdminNotificationsPage = lazyNamed(() => import('@/react/features/admin/notifications/pages/AdminNotificationsPage'), 'AdminNotificationsPage');
const AdminSettingsPage = lazyNamed(() => import('@/react/features/admin/settings/pages/AdminSettingsPage'), 'AdminSettingsPage');

const ClinicianDashboardPage = lazyNamed(() => import('@/react/features/clinician/dashboard/pages/ClinicianDashboardPage'), 'ClinicianDashboardPage');
const ClinicianPatientsPage = lazyNamed(() => import('@/react/features/clinician/patients/pages/ClinicianPatientsPage'), 'ClinicianPatientsPage');
const AgendaPage = lazyNamed(() => import('@/react/features/clinician/agenda/pages/AgendaPage'), 'AgendaPage');
const AppointmentPage = lazyNamed(() => import('@/react/features/clinician/appointments/pages/AppointmentPage'), 'AppointmentPage');
const MessagesPage = lazyNamed(() => import('@/react/features/clinician/messages/pages/MessagesPage'), 'MessagesPage');
const ClinicianNotificationsPage = lazyNamed(() => import('@/react/features/clinician/notifications/pages/ClinicianNotificationsPage'), 'ClinicianNotificationsPage');

const NutritionistDashboardPage = lazyNamed(() => import('@/react/features/nutritionist/dashboard/pages/NutritionistDashboardPage'), 'NutritionistDashboardPage');
const NutritionistPatientsPage = lazyNamed(() => import('@/react/features/nutritionist/patients/pages/NutritionistPatientsPage'), 'NutritionistPatientsPage');
const MealPlansPage = lazyNamed(() => import('@/react/features/nutritionist/plans/pages/MealPlansPage'), 'MealPlansPage');
const FoodsPage = lazyNamed(() => import('@/react/features/nutritionist/foods/pages/FoodsPage'), 'FoodsPage');
const NotificationPages = lazyNamed(() => import('@/react/features/nutritionist/notifications/pages/NotificationPages'), 'NotificationPages');
const MessagesPages = lazyNamed(() => import('@/react/features/nutritionist/messages/pages/MessagesPages'), 'MessagesPages');
const AppointmentsPages = lazyNamed(() => import('@/react/features/nutritionist/appointments/pages/AppointmentsPages'), 'AppointmentsPages');
const AgendaPages = lazyNamed(() => import('@/react/features/nutritionist/agenda/pages/AgendaPages'), 'AgendaPages');
const NutritionistPatientRecordPage = lazyNamed(() => import('@/react/features/nutritionist/patients/pages/NutritionistPatientRecordPage'), 'NutritionistPatientRecordPage');
const NutritionistPatientRecordInitPage = lazyNamed(() => import('@/react/features/nutritionist/patients/pages/NutritionistPatientRecordInitPage'), 'NutritionistPatientRecordInitPage');
const NutritionistPatientRecordClosedPage = lazyNamed(() => import('@/react/features/nutritionist/patients/pages/NutritionistPatientRecordClosedPage'), 'NutritionistPatientRecordClosedPage');

const PatientDashboardPage = lazyNamed(() => import('@/react/features/patient/dashboard/pages/PatientDashboardPage'), 'PatientDashboardPage');
const MeasurementsPage = lazyNamed(() => import('@/react/features/patient/health/pages/MeasurementsPage'), 'MeasurementsPage');
const MedicalRecordPage = lazyNamed(() => import('@/react/features/patient/medical-record/pages/MedicalRecordPage'), 'MedicalRecordPage');
const TreatmentsPage = lazyNamed(() => import('@/react/features/patient/treatments/pages/TreatmentsPage'), 'TreatmentsPage');
const DosesPage = lazyNamed(() => import('@/react/features/patient/doses/pages/DosesPage'), 'DosesPage');
const PatientAppointmentsPage = lazyNamed(() => import('@/react/features/patient/appointments/pages/PatientAppointmentsPage'), 'PatientAppointmentsPage');
const BookingPage = lazyNamed(() => import('@/react/features/patient/booking/pages/BookingPage'), 'BookingPage');
const PatientMessagesPage = lazyNamed(() => import('@/react/features/patient/messages/pages/PatientMessagesPage'), 'PatientMessagesPage');
const PatientNotificationsPage = lazyNamed(() => import('@/react/features/patient/notifications/pages/PatientNotificationsPage'), 'PatientNotificationsPage');
const TeamPage = lazyNamed(() => import('@/react/features/patient/team/pages/TeamPage'), 'TeamPage');
const ProfilePage = lazyNamed(() => import('@/react/features/profile/pages/ProfilePage'), 'ProfilePage');
const EstablishmentDetailPage = lazyNamed(() => import('@/react/features/admin/establishments/pages/EstablishmentDetailPage'), 'EstablishmentDetailPage');
const EstablishmentsPage = lazyNamed(() => import('@/react/features/admin/establishments/pages/EstablishmentsPage'), 'EstablishmentsPage');
const OrganizationReportPage = lazyNamed(() => import('@/react/features/admin/reports/pages/OrganizationReportPage'), 'OrganizationReportPage');
const ReportVerificationPage = lazyNamed(() => import('@/react/features/admin/reports/pages/ReportVerificationPage'), 'ReportVerificationPage');
const MedicationsPage = lazyNamed(() => import('@/react/features/admin/medications/pages/MedicationsPage'), 'MedicationsPage');
const ClinicianPatientRecordPage = lazyNamed(() => import('@/react/features/clinician/patients/pages/ClinicianPatientRecordPage'), 'ClinicianPatientRecordPage');
const ClinicianPatientRecordInitPage = lazyNamed(() => import('@/react/features/clinician/patients/pages/ClinicianPatientRecordInitPage'), 'ClinicianPatientRecordInitPage');
const ClinicianPatientRecordClosedPage = lazyNamed(() => import('@/react/features/clinician/patients/pages/ClinicianPatientRecordClosedPage'), 'ClinicianPatientRecordClosedPage');
const NutritionPage = lazyNamed(() => import('@/react/features/patient/nutrition/pages/NutritionPage'), 'NutritionPage');
const ProfessionalCreatePage = lazyNamed(() => import('@/react/features/admin/professionals/pages/ProfessionalCreatePage'), 'ProfessionalCreatePage');
const AdminExternalFollowsPage = lazyNamed(() => import('@/react/features/admin/external-follows/pages/AdminExternalFollowsPage'), 'AdminExternalFollowsPage');
const MyExternalFollowsPage = lazyNamed(() => import('@/react/features/admin/external-follows/pages/MyExternalFollowsPage'), 'MyExternalFollowsPage');
const AcceptInvitationPage = lazyNamed(() => import('@/react/features/admin/external-follows/pages/AcceptInvitationPage'), 'AcceptInvitationPage');

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

function PageLoading() {
    return (
        <div className="route-loading" role="status" aria-label="Chargement de la page">
            <Loading text="Chargement..." />
        </div>
    );
}

function AppRoutesContent() {
    const location = useLocation();

    return (
        <ErrorBoundary key={location.pathname}>
            <Suspense fallback={<PageLoading />}>
                <Routes>
                    <Route path="/" element={<HomePage />} />

                    <Route path="/verify/report" element={<ReportVerificationPage />} />

                    <Route path="/invite/:token" element={<AcceptInvitationPage />} />

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
                        {/*<Route path="audit" element={<AuditPage />} />*/}
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
                        {/*<Route path="establishments" element={<EstablishmentsPage />} />*/}
                        {/*<Route path="departments" element={<DepartmentsPage />} />*/}
                        <Route path="professionals" element={<ProfessionalsPage />} />
                        {/*<Route path="members" element={<MembersPage />} />*/}
                        <Route path="patients" element={<PatientsPage />} />
                        <Route path="medications" element={<MedicationsPage />} />
                        <Route path="external-follows" element={<AdminExternalFollowsPage />} />
                        <Route path="reports" element={<OrganizationReportPage />} />
                        {/*<Route path="appointments" element={<AppointmentsPage />} />*/}
                        {/*<Route path="activity" element={<ActivityPage />} />*/}
                        <Route path="notifications" element={<AdminNotificationsPage />} />
                        {/*<Route path="settings" element={<AdminSettingsPage />} />*/}
                        <Route path="establishments/:type/:id" element={<EstablishmentDetailPage />} />
                        <Route path="professionals/new" element={<ProfessionalCreatePage />} />
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
                        <Route path="external-follows" element={<MyExternalFollowsPage />} />
                        <Route path="patients/:patientId/record" element={<ClinicianPatientRecordPage />} />
                        <Route path="patients/:patientId/record/init" element={<ClinicianPatientRecordInitPage />} />
                        <Route path="patients/:patientId/record/closed" element={<ClinicianPatientRecordClosedPage />} />

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
                        {/*<Route path="plans" element={<MealPlansPage />} />*/}
                        <Route path="foods" element={<FoodsPage />} />
                        <Route path="agenda" element={<AgendaPages />} />
                        <Route path="appointments" element={<AppointmentsPages />} />
                        <Route path="messages" element={<MessagesPages />} />
                        <Route path="notifications" element={<NotificationPages />} />
                        <Route path="external-follows" element={<MyExternalFollowsPage />} />
                        <Route path="patients/:patientId/record" element={<NutritionistPatientRecordPage />} />
                        <Route path="patients/:patientId/record/init" element={<NutritionistPatientRecordInitPage />} />
                        <Route path="patients/:patientId/record/closed" element={<NutritionistPatientRecordClosedPage />} />
                    </Route>

                    <Route
                        path="/patient"
                        element={
                            <ProtectedRoute>
                                <MainLayout />
                            </ProtectedRoute>
                        }
                    >
                        <Route index element={<Navigate to="/patient/summary" replace />} />
                        <Route path="summary" element={<PatientDashboardPage />} />
                        <Route path="measurements" element={<MeasurementsPage />} />
                        <Route path="record" element={<MedicalRecordPage />} />
                        <Route path="treatments" element={<TreatmentsPage />} />
                        <Route path="doses" element={<DosesPage />} />
                        <Route path="appointments" element={<PatientAppointmentsPage />} />
                        {/*<Route path="book" element={<BookingPage />} />*/}
                        <Route path="messages" element={<PatientMessagesPage />} />
                        <Route path="notifications" element={<PatientNotificationsPage />} />
                        <Route path="team" element={<TeamPage />} />
                        <Route path="nutrition" element={<NutritionPage />} />
                    </Route>

                    <Route
                        path="/profile"
                        element={
                            <ProtectedRoute>
                                <MainLayout>
                                    <ProfilePage />
                                </MainLayout>
                            </ProtectedRoute>
                        }
                    />

                    {/* Fallback pour /app, au cas où */}
                    <Route path="/app" element={<Navigate to="/root/dashboard" replace />} />

                    <Route path="*" element={<Navigate to="/" replace />} />
                </Routes>
            </Suspense>
        </ErrorBoundary>
    );
}

export default function AppRoutes() {
    return (
        <BrowserRouter>
            <AppRoutesContent />
        </BrowserRouter>
    );
}