// import { SidebarConfig } from './sidebar.types';
import {
    DashboardIcon,
    UsersIcon,
    OrganisationIcon,
    PatientsIcon,
    AppointmentsIcon,
    MessagesIcon,
    NotificationsIcon,
    ReportsIcon,
    AdministrationIcon,
    RolesIcon,
    PermissionsIcon,
    AuditIcon,
    DataAccessIcon,
    ProfileIcon,
    SettingsIcon,
} from './sidebar.icons';
import {SidebarConfig} from "@/react/hook-components/Navigation/Sidebar/types";

export const SIDEBAR_CONFIG: SidebarConfig = [
    {
        id: 'principal',
        label: 'Principal',
        items: [
            {
                id: 'dashboard',
                label: 'Tableau de bord',
                icon: <DashboardIcon />,
                route: '/dashboard',
                permission: 'DASHBOARD_VIEW',
            },
        ],
    },
    {
        id: 'gestion',
        label: 'Gestion',
        items: [
            { id: 'users', label: 'Utilisateurs', icon: <UsersIcon />, route: '/users', permission: 'USER_VIEW' },
            { id: 'org', label: 'Organisation', icon: <OrganisationIcon />, route: '/organisation', permission: 'ORG_VIEW' },
            { id: 'patients', label: 'Patients', icon: <PatientsIcon />, route: '/patients', permission: 'PATIENT_VIEW' },
            { id: 'appointments', label: 'Rendez-vous', icon: <AppointmentsIcon />, route: '/appointments', permission: 'APPOINTMENT_VIEW' },
        ],
    },
    {
        id: 'activite',
        label: 'Activité',
        items: [
            { id: 'messages', label: 'Messages', icon: <MessagesIcon />, route: '/messages', permission: 'MESSAGE_VIEW' },
            { id: 'notifications', label: 'Notifications', icon: <NotificationsIcon />, route: '/notifications', permission: 'NOTIFICATION_VIEW' },
            { id: 'reports', label: 'Rapports', icon: <ReportsIcon />, route: '/reports', permission: 'REPORT_VIEW' },
        ],
    },
    {
        id: 'administration',
        label: 'Administration',
        items: [
            {
                id: 'admin',
                label: 'Administration',
                icon: <AdministrationIcon />,
                permission: 'ADMIN_VIEW',
                children: [
                    { id: 'roles', label: 'Rôles', icon: <RolesIcon />, route: '/admin/roles', permission: 'ROLE_VIEW' },
                    { id: 'permissions', label: 'Permissions', icon: <PermissionsIcon />, route: '/admin/permissions', permission: 'PERMISSION_VIEW' },
                    { id: 'audit', label: 'Audit', icon: <AuditIcon />, route: '/admin/audit', permission: 'AUDIT_VIEW' },
                    { id: 'data-access', label: 'Accès aux données', icon: <DataAccessIcon />, route: '/admin/data-access', permission: 'DATA_ACCESS_VIEW' },
                ],
            },
        ],
    },
    {
        id: 'compte',
        label: 'Compte',
        items: [
            { id: 'profile', label: 'Mon profil', icon: <ProfileIcon />, route: '/profile', permission: 'PROFILE_VIEW' },
            { id: 'settings', label: 'Paramètres', icon: <SettingsIcon />, route: '/settings', permission: 'SETTINGS_VIEW' },
        ],
    },
];
