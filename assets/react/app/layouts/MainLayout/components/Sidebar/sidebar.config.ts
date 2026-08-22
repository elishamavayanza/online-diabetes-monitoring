import {SidebarConfig} from "@/react/hook-components/Navigation/Sidebar/types";

export const SIDEBAR_CONFIG: SidebarConfig = [
    {
        id: 'principal',
        label: 'Principal',
        items: [
            {
                id: 'dashboard',
                label: 'Tableau de bord',
                icon: '🏠',
                route: '/dashboard',
                permission: 'DASHBOARD_VIEW',
            },
        ],
    },
    {
        id: 'gestion',
        label: 'Gestion',
        items: [
            { id: 'users', label: 'Utilisateurs', icon: '👥', route: '/users', permission: 'USER_VIEW' },
            { id: 'org', label: 'Organisation', icon: '🏢', route: '/organisation', permission: 'ORG_VIEW' },
            { id: 'patients', label: 'Patients', icon: '🩺', route: '/patients', permission: 'PATIENT_VIEW' },
            { id: 'appointments', label: 'Rendez-vous', icon: '📅', route: '/appointments', permission: 'APPOINTMENT_VIEW' },
        ],
    },
    {
        id: 'activite',
        label: 'Activité',
        items: [
            { id: 'messages', label: 'Messages', icon: '💬', route: '/messages', permission: 'MESSAGE_VIEW' },
            { id: 'notifications', label: 'Notifications', icon: '🔔', route: '/notifications', permission: 'NOTIFICATION_VIEW' },
            { id: 'reports', label: 'Rapports', icon: '📊', route: '/reports', permission: 'REPORT_VIEW' },
        ],
    },
    {
        id: 'administration',
        label: 'Administration',
        items: [
            {
                id: 'admin',
                label: 'Administration',
                icon: '🔐',
                permission: 'ADMIN_VIEW',
                children: [
                    { id: 'roles', label: 'Rôles', icon: '🔑', route: '/admin/roles', permission: 'ROLE_VIEW' },
                    { id: 'permissions', label: 'Permissions', icon: '🛡️', route: '/admin/permissions', permission: 'PERMISSION_VIEW' },
                    { id: 'audit', label: 'Audit', icon: '📋', route: '/admin/audit', permission: 'AUDIT_VIEW' },
                    { id: 'data-access', label: 'Accès aux données', icon: '🗄️', route: '/admin/data-access', permission: 'DATA_ACCESS_VIEW' },
                ],
            },
        ],
    },
    {
        id: 'compte',
        label: 'Compte',
        items: [
            { id: 'profile', label: 'Mon profil', icon: '👤', route: '/profile', permission: 'PROFILE_VIEW' },
            { id: 'settings', label: 'Paramètres', icon: '⚙️', route: '/settings', permission: 'SETTINGS_VIEW' },
        ],
    },
];
