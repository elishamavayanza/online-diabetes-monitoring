import { SidebarConfig } from "@/react/hook-components/Navigation/Sidebar/types";
import {
    DashboardIcon,
    UsersIcon,
    OrganisationIcon,
    PatientsIcon,
    AppointmentsIcon,
    MessagesIcon,
    NotificationsIcon,
    AdministrationIcon,
    RolesIcon,
    AuditIcon,
    SettingsIcon,
    EstablishmentIcon,
    DepartmentIcon,
    ProfessionalsIcon,
    MembersIcon,
    ActivityIcon,
    PlansIcon,
    FoodIcon,
    SummaryIcon,
    MeasuresIcon,
    FileIcon,
    TreatmentIcon,
    DosesIcon,
    CalendarIcon,
    TeamIcon,
    ConfigIcon, BookAppointmentIcon,
} from "./sidebar.icons";

export type UserRole = "ROOT" | "ADMIN" | "CLINICIAN" | "NUTRITIONIST" | "PATIENT";

export const SIDEBAR_CONFIGS: Record<UserRole, SidebarConfig> = {
    ROOT: [
        {
            id: "dashboard-root",
            label: "Tableau de bord",
            items: [
                { id: "root-dashboard", label: "Vue générale", icon: <DashboardIcon />, route: "/root/dashboard", permission: "DASHBOARD_VIEW" },
            ],
        },
        {
            id: "platform",
            label: "Plateforme",
            items: [
                { id: "organisations", label: "Organisations", icon: <OrganisationIcon />, route: "/root/organisations", permission: "ORGANISATION_VIEW" },
                { id: "users", label: "Utilisateurs", icon: <UsersIcon />, route: "/root/users", permission: "USER_VIEW" },
                { id: "roles-permissions", label: "Rôles & permissions", icon: <RolesIcon />, route: "/root/roles", permission: "ROLE_VIEW" },
            ],
        },
        {
            id: "system",
            label: "Système",
            items: [
                { id: "root-notifications", label: "Notifications", icon: <NotificationsIcon />, route: "/root/notifications", permission: "NOTIFICATION_VIEW" },
                { id: "audit-logs", label: "Journaux d’audit", icon: <AuditIcon />, route: "/root/audit", permission: "AUDIT_VIEW" },
            ],
        },
        {
            id: "settings-root",
            label: "Paramètres",
            items: [
                { id: "root-settings", label: "Configuration", icon: <SettingsIcon />, route: "/root/settings", permission: "SETTINGS_VIEW" },
            ],
        },
    ],

    ADMIN: [
        {
            id: "dashboard-admin",
            label: "Tableau de bord",
            items: [
                { id: "admin-dashboard", label: "Vue générale", icon: <DashboardIcon />, route: "/admin/dashboard", permission: "DASHBOARD_VIEW" },
            ],
        },
        {
            id: "organisation-admin",
            label: "Organisation",
            items: [
                { id: "establishments", label: "Établissements", icon: <EstablishmentIcon />, route: "/admin/establishments", permission: "ESTABLISHMENT_VIEW" },
            ],
        },
        {
            id: "personnel",
            label: "Personnel",
            items: [
                { id: "professionals", label: "Professionnels", icon: <ProfessionalsIcon />, route: "/admin/professionals", permission: "PROFESSIONAL_VIEW" },
                { id: "members", label: "Membres", icon: <MembersIcon />, route: "/admin/members", permission: "MEMBER_VIEW" },
            ],
        },
        {
            id: "patients-admin",
            label: "Patients",
            items: [
                { id: "patients-all", label: "Patients", icon: <PatientsIcon />, route: "/admin/patients", permission: "PATIENT_VIEW" },
            ],
        },
        {
            id: "activity-admin",
            label: "Activité",
            items: [
                { id: "admin-appointments", label: "Rendez-vous", icon: <AppointmentsIcon />, route: "/admin/appointments", permission: "APPOINTMENT_VIEW" },
                { id: "activity-log", label: "Activité", icon: <ActivityIcon />, route: "/admin/activity", permission: "ACTIVITY_VIEW" },
                { id: "admin-notifications", label: "Notifications", icon: <NotificationsIcon />, route: "/admin/notifications", permission: "NOTIFICATION_VIEW" },
            ],
        },
        {
            id: "settings-admin",
            label: "Paramètres",
            items: [
                { id: "org-settings", label: "Organisation", icon: <OrganisationIcon />, route: "/admin/settings", permission: "SETTINGS_VIEW" },
            ],
        },
    ],

    CLINICIAN: [
        {
            id: "home-clinician",
            label: "Accueil",
            items: [
                { id: "clinician-dashboard", label: "Tableau de bord", icon: <DashboardIcon />, route: "/clinician/dashboard", permission: "DASHBOARD_VIEW" },
            ],
        },
        {
            id: "patients-clinician",
            label: "Patients",
            items: [
                { id: "my-patients", label: "Mes patients", icon: <PatientsIcon />, route: "/clinician/my-patients", permission: "PATIENT_VIEW" },
            ],
        },
        {
            id: "appointments-clinician",
            label: "Rendez-vous",
            items: [
                { id: "clinician-agenda", label: "Agenda", icon: <AppointmentsIcon />, route: "/clinician/agenda", permission: "APPOINTMENT_VIEW" },
                { id: "clinician-appointments", label: "Rendez-vous", icon: <CalendarIcon />, route: "/clinician/appointments", permission: "APPOINTMENT_VIEW" },
            ],
        },
        {
            id: "communication-clinician",
            label: "Communication",
            items: [
                { id: "clinician-messages", label: "Messages", icon: <MessagesIcon />, route: "/clinician/messages", permission: "MESSAGE_VIEW" },
                { id: "clinician-notifications", label: "Notifications", icon: <NotificationsIcon />, route: "/clinician/notifications", permission: "NOTIFICATION_VIEW" },
            ],
        },
    ],

    NUTRITIONIST: [
        {
            id: "home-nutritionist",
            label: "Accueil",
            items: [
                { id: "nutritionist-dashboard", label: "Tableau de bord", icon: <DashboardIcon />, route: "/nutritionist/dashboard", permission: "DASHBOARD_VIEW" },
            ],
        },
        {
            id: "patients-nutritionist",
            label: "Patients",
            items: [
                { id: "nutritionist-my-patients", label: "Mes patients", icon: <PatientsIcon />, route: "/nutritionist/my-patients", permission: "PATIENT_VIEW" },
            ],
        },
        {
            id: "nutrition",
            label: "Nutrition",
            items: [
                { id: "meal-plans", label: "Plans alimentaires", icon: <PlansIcon />, route: "/nutritionist/plans", permission: "NUTRITION_PLAN_VIEW" },
                { id: "foods", label: "Aliments", icon: <FoodIcon />, route: "/nutritionist/foods", permission: "FOOD_VIEW" },
            ],
        },
        {
            id: "appointments-nutritionist",
            label: "Rendez-vous",
            items: [
                { id: "nutritionist-agenda", label: "Agenda", icon: <AppointmentsIcon />, route: "/nutritionist/agenda", permission: "APPOINTMENT_VIEW" },
                { id: "nutritionist-appointments", label: "Rendez-vous", icon: <CalendarIcon />, route: "/nutritionist/appointments", permission: "APPOINTMENT_VIEW" },
            ],
        },
        {
            id: "communication-nutritionist",
            label: "Communication",
            items: [
                { id: "nutritionist-messages", label: "Messages", icon: <MessagesIcon />, route: "/nutritionist/messages", permission: "MESSAGE_VIEW" },
                { id: "nutritionist-notifications", label: "Notifications", icon: <NotificationsIcon />, route: "/nutritionist/notifications", permission: "NOTIFICATION_VIEW" },
            ],
        },
    ],

    PATIENT: [
        {
            id: "home-patient",
            label: "Accueil",
            items: [
                { id: "health-summary", label: "Résumé de santé", icon: <SummaryIcon />, route: "/patient/summary", permission: "SUMMARY_VIEW" },
            ],
        },
        {
            id: "my-health",
            label: "Ma santé",
            items: [
                { id: "measurements", label: "Mesures", icon: <MeasuresIcon />, route: "/patient/measurements", permission: "MEASUREMENT_VIEW" },
                { id: "health-record", label: "Mon dossier", icon: <FileIcon />, route: "/patient/record", permission: "HEALTH_RECORD_VIEW" },
            ],
        },
        {
            id: "treatment",
            label: "Traitement",
            items: [
                { id: "my-treatments", label: "Mes traitements", icon: <TreatmentIcon />, route: "/patient/treatments", permission: "TREATMENT_VIEW" },
                { id: "doses", label: "Mes prises", icon: <DosesIcon />, route: "/patient/doses", permission: "DOSE_VIEW" },
            ],
        },
        {
            id: "appointments-patient",
            label: "Rendez-vous",
            items: [
                { id: "my-appointments", label: "Mes rendez-vous", icon: <AppointmentsIcon />, route: "/patient/appointments", permission: "APPOINTMENT_VIEW" },
                { id: "book-appointment", label: "Prendre rendez-vous", icon: <BookAppointmentIcon />, route: "/patient/book", permission: "APPOINTMENT_CREATE" },
            ],
        },
        {
            id: "communication-patient",
            label: "Communication",
            items: [
                { id: "patient-messages", label: "Messages", icon: <MessagesIcon />, route: "/patient/messages", permission: "MESSAGE_VIEW" },
                { id: "patient-notifications", label: "Notifications", icon: <NotificationsIcon />, route: "/patient/notifications", permission: "NOTIFICATION_VIEW" },
                { id: "my-team", label: "Mon équipe", icon: <TeamIcon />, route: "/patient/team", permission: "TEAM_VIEW" },
            ],
        },
    ],
};
