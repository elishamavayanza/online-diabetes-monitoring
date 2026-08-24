import React, { useState } from 'react';
import { Footer } from '@/react/components/Navigation/Footer';
import { RightSidebar } from "@/react/components/Navigation/RightSidebar";
import { Sidebar } from "@/react/components/Navigation/Sidebar";
import { useAuth } from '@/react/app/providers/AuthProvider';
import { SIDEBAR_CONFIGS, UserRole } from './components/Sidebar/sidebar.config';
import './MainLayout.scss';
import { Header } from '@/react/components/Navigation/Header';
import logo from '@/images/logo_with.png';
import { Avatar } from "@/react/components/UI/Avatar";
import { PopoverMenu } from "@/react/components/UI/PopoverMenu";
import { LogoutIcon, ProfileIcon } from "@/react/app/layouts/MainLayout/components/Sidebar/sidebar.icons";
import { useIsMobile } from '@/react/hooks/useIsMobile';
import { useIsPortrait } from '@/react/hooks/useIsPortrait';
import {PanelRightIcon} from "@/react/app/layouts/MainLayout/components/PanelRightIcon";   // ← nouveau

interface MainLayoutProps {
    children: React.ReactNode;
    showHeader?: boolean;
    showSidebar?: boolean;
    showFooter?: boolean;
    showRightSidebar?: boolean;
    sidebarContent?: React.ReactNode;
    rightSidebarContent?: React.ReactNode;
    rightSidebarProps?: Omit<React.ComponentProps<typeof RightSidebar>, 'children'>;
    headerProps?: React.ComponentProps<typeof Header>;
    sidebarProps?: React.ComponentProps<typeof Sidebar>;
    footerProps?: React.ComponentProps<typeof Footer>;
    className?: string;
}

export function MainLayout({
                               children,
                               showHeader = true,
                               showSidebar = true,
                               showFooter = true,
                               showRightSidebar = false,
                               sidebarContent,
                               rightSidebarContent,
                               rightSidebarProps,
                               headerProps,
                               sidebarProps,
                               footerProps,
                               className = '',
                           }: MainLayoutProps) {
    const { user, logout } = useAuth();
    const isMobile = useIsMobile();
    const isPortrait = useIsPortrait();

    // On considère "compact" si mobile OU portrait (tablette en portrait, desktop vertical, etc.)
    const isCompact = isMobile || isPortrait;

    const [rightSidebarOpen, setRightSidebarOpen] = useState(!isCompact);  // fermé par défaut si compact

    const permissions = user?.permissions ?? [];
    const userRole = user?.role as UserRole | undefined;
    const menuConfig = userRole ? SIDEBAR_CONFIGS[userRole] : SIDEBAR_CONFIGS.ROOT;

    return (
        <div className={`main-layout ${className}`.trim()}>
            {/* Bouton flottant pour ouvrir/fermer le panneau droit en mode compact */}
            {isCompact && showRightSidebar && (
                <button
                    className="main-layout__floating-toggle-right"
                    onClick={() => setRightSidebarOpen((prev) => !prev)}
                    aria-label={rightSidebarOpen ? 'Fermer le panneau droit' : 'Ouvrir le panneau droit'}
                    title={rightSidebarOpen ? 'Fermer le panneau droit' : 'Ouvrir le panneau droit'}
                >
                    <PanelRightIcon open={rightSidebarOpen} />
                </button>
            )}

            <div className="main-layout__container">
                {showSidebar && user && (
                    <aside className="main-layout__sidebar-left">
                        {sidebarContent || (
                            <Sidebar
                                groups={menuConfig}
                                userPermissions={permissions}
                                collapsible
                                defaultCollapsed={isCompact}   // replié si compact
                                activeId="dashboard"
                                header={
                                    <div className="sidebar-brand">
                                        <img src={logo} alt="OnlineDIAB" className="sidebar-brand__logo" />
                                        <span className="sidebar-brand__title">OnlineDIAB</span>
                                    </div>
                                }
                                footer={
                                    <PopoverMenu
                                        placement="top"
                                        items={[
                                            {
                                                id: 'profile',
                                                label: 'Mon profil',
                                                icon: <ProfileIcon />,
                                                onClick: () => console.log('Profil'),
                                            },
                                            {
                                                id: 'separator',
                                                label: '',
                                                separator: true,
                                            },
                                            {
                                                id: 'logout',
                                                label: 'Déconnexion',
                                                icon: <LogoutIcon />,
                                                danger: true,
                                                onClick: () => {
                                                    logout();
                                                },
                                            },
                                        ]}
                                        trigger={
                                            <div className="sidebar-user-menu">
                                                <Avatar
                                                    src={user.photoUrl || undefined}
                                                    name={user.name}
                                                    size="medium"
                                                    shape="circle"
                                                    status="online"
                                                />
                                                <div className="sidebar-user-menu__info">
                                                    <span className="sidebar-user-menu__name">{user.name}</span>
                                                    <span className="sidebar-user-menu__role">{user.role || 'Utilisateur'}</span>
                                                </div>
                                            </div>
                                        }
                                    />
                                }
                            />
                        )}
                    </aside>
                )}

                <div className="main-layout__main">
                    {showHeader && (
                        <header className="main-layout__header" />
                    )}

                    <div className="main-layout__body">
                        <main className="main-layout__content">
                            {children}
                        </main>

                        {showRightSidebar && rightSidebarOpen && (
                            <RightSidebar
                                collapsible
                                size="medium"
                                minWidth={250}
                                maxWidth={500}
                                closeThreshold={80}
                                collapsedWidth={35}
                                {...rightSidebarProps}
                            >
                                {rightSidebarContent || (
                                    <div className="main-layout__right-content">
                                        <p>Contenu par défaut de la sidebar droite.</p>
                                        <ul>
                                            <li>Information 1</li>
                                            <li>Information 2</li>
                                            <li>Information 3</li>
                                        </ul>
                                    </div>
                                )}
                            </RightSidebar>
                        )}
                    </div>

                    {showFooter && (
                        <footer className="main-layout__footer" />
                    )}
                </div>
            </div>
        </div>
    );
}
