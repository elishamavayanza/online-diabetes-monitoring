import React from 'react';
import { Header } from '@/react/components/Navigation/Header';
import { Sidebar } from '@/react/components/Navigation/Sidebar';
import { Footer } from '@/react/components/Navigation/Footer';
import {RightSidebar} from "@/react/components/Navigation/RightSidebar";
import './MainLayout.scss';

interface MainLayoutProps {
    children: React.ReactNode;
    showHeader?: boolean;
    showSidebar?: boolean;
    showFooter?: boolean;
    showRightSidebar?: boolean;
    sidebarContent?: React.ReactNode;
    rightSidebarContent?: React.ReactNode;
    headerProps?: React.ComponentProps<typeof Header>;
    sidebarProps?: React.ComponentProps<typeof Sidebar>;
    footerProps?: React.ComponentProps<typeof Footer>;
    className?: string;
}

export function MainLayout({
                               children,
                               showHeader = true,
                               showSidebar = true,
                               showFooter = false,
                               showRightSidebar = false,
                               sidebarContent,
                               rightSidebarContent,
                               headerProps,
                               sidebarProps,
                               footerProps,
                               className = '',
                           }: MainLayoutProps) {
    return (
        <div className={`main-layout ${className}`.trim()}>
            {showHeader && (
                <header className="main-layout__header">
                    <Header
                        logo="OnlineDIAB"
                        navItems={[]}
                        {...headerProps}
                    />
                </header>
            )}

            <div className="main-layout__body">
                {showSidebar && (
                    <aside className="main-layout__sidebar-left">
                        {sidebarContent || (
                            <Sidebar
                                items={[]}
                                collapsible
                                {...sidebarProps}
                            />
                        )}
                    </aside>
                )}

                <main className="main-layout__content">{children}</main>

                {showRightSidebar && (
                    <aside className="main-layout__sidebar-right">
                        {rightSidebarContent || (
                            <RightSidebar
                                collapsible
                                defaultCollapsed={false}
                                size="medium"
                                className="right-sidebar--full-width"
                            >
                                <p>Contenu par défaut de la sidebar droite.</p>
                            </RightSidebar>
                        )}
                    </aside>
                )}
            </div>

            {showFooter && (
                <footer className="main-layout__footer">
                    <Footer
                        brand="OnlineDIAB"
                        sections={[]}
                        {...footerProps}
                    />
                </footer>
            )}
        </div>
    );
}
