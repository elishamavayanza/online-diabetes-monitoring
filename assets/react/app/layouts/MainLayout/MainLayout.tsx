import React, { useState } from 'react';
import { Footer } from '@/react/components/Navigation/Footer';
import { RightSidebar } from "@/react/components/Navigation/RightSidebar";
import { Sidebar } from "@/react/components/Navigation/Sidebar";
import { useAuth } from '@/react/app/providers/AuthProvider';
import { SIDEBAR_CONFIG } from './components/Sidebar/sidebar.config';
import './MainLayout.scss';
import { Header } from '@/react/components/Navigation/Header';
import logo from '@/images/logo_with.png';

interface MainLayoutProps {
    children: React.ReactNode;
    showHeader?: boolean;
    showSidebar?: boolean;
    showFooter?: boolean;
    showRightSidebar?: boolean;
    sidebarContent?: React.ReactNode;
    rightSidebarContent?: React.ReactNode;
    rightSidebarProps?: Omit<React.ComponentProps<typeof RightSidebar>, 'children'>;
    headerProps?: React.ComponentProps<typeof Header>; // gardé pour API future, non utilisé
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
    const { user } = useAuth();
    const [mobileSidebarOpen, setMobileSidebarOpen] = useState(false);
    const [rightSidebarOpen, setRightSidebarOpen] = useState(true);

    const permissions = user?.permissions ?? [];

    return (
        <div className={`main-layout ${className}`.trim()}>
            <div className="main-layout__container">
                {showSidebar && user && (
                    <aside className="main-layout__sidebar-left">
                        {sidebarContent || (
                            <Sidebar
                                groups={SIDEBAR_CONFIG}
                                userPermissions={permissions}
                                collapsible
                                defaultCollapsed={false}
                                activeId="dashboard"
                                mobileOpen={mobileSidebarOpen}
                                onMobileClose={() => setMobileSidebarOpen(false)}
                                header={
                                    <div className="sidebar-brand">
                                        <img src={logo} alt="OnlineDIAB" className="sidebar-brand__logo" />
                                        <span className="sidebar-brand__title">OnlineDIAB</span>
                                    </div>
                                }
                                footer={<div>{user.name}</div>}
                                {...sidebarProps}
                            />
                        )}
                    </aside>
                )}

                <div className="main-layout__main">
                    {/* Header vide, même hauteur que le footer */}
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

                    {/* Footer vide */}
                    {showFooter && (
                        <footer className="main-layout__footer" />
                    )}
                </div>
            </div>
        </div>
    );
}
