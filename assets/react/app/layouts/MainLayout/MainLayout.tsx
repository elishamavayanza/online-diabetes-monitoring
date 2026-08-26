import React, { useRef, useState } from 'react';
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
import { PanelRightIcon } from "@/react/app/layouts/MainLayout/components/PanelRightIcon";
import { Outlet, useLocation, useNavigate } from 'react-router-dom';
import { useActionHistory } from './contexts/ActionHistoryContext';

// ---------- Icônes hamburger / fermer ----------
const MenuIcon = () => (
    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" strokeWidth="2">
        <line x1="3" y1="6" x2="21" y2="6" />
        <line x1="3" y1="12" x2="21" y2="12" />
        <line x1="3" y1="18" x2="21" y2="18" />
    </svg>
);

const CloseIcon = () => (
    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" strokeWidth="2">
        <line x1="18" y1="6" x2="6" y2="18" />
        <line x1="6" y1="6" x2="18" y2="18" />
    </svg>
);

const BackIcon = () => (
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <path d="M19 12H5" />
        <polyline points="12 19 5 12 12 5" />
    </svg>
);

interface MainLayoutProps {
    children?: React.ReactNode;
    // children: React.ReactNode;
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
    const location = useLocation();
    const navigate = useNavigate();
    const { undoLastAction } = useActionHistory();

    const isCompact = isMobile || isPortrait;

    const [mobileSidebarOpen, setMobileSidebarOpen] = useState(false);
    const [rightSidebarOpen, setRightSidebarOpen] = useState(!isCompact);

    const permissions = user?.permissions ?? [];
    const userRole = user?.role as UserRole | undefined;
    const menuConfig = userRole ? SIDEBAR_CONFIGS[userRole] : SIDEBAR_CONFIGS.ROOT;

    // ---------- Gestion tactile (mobile uniquement) ----------
    const mainTouchStart = useRef<{ x: number; y: number } | null>(null);
    const sidebarTouchStart = useRef<{ x: number; y: number } | null>(null);

    const handleMainTouchStart = (e: React.TouchEvent) => {
        if (!isMobile || mobileSidebarOpen) return;
        const touch = e.touches[0];
        mainTouchStart.current = { x: touch.clientX, y: touch.clientY };
    };

    const handleMainTouchEnd = (e: React.TouchEvent) => {
        if (!isMobile || mobileSidebarOpen || !mainTouchStart.current) return;
        const touch = e.changedTouches[0];
        const dx = touch.clientX - mainTouchStart.current.x;
        const dy = touch.clientY - mainTouchStart.current.y;

        // Balayage vers la gauche (dx < 0) → ouvre
        if (Math.abs(dx) > 60 && Math.abs(dy) < 30 && dx < 0) {
            setMobileSidebarOpen(true);
        }
        mainTouchStart.current = null;
    };

    const handleSidebarTouchStart = (e: React.TouchEvent) => {
        if (!isMobile || !mobileSidebarOpen) return;
        const touch = e.touches[0];
        sidebarTouchStart.current = { x: touch.clientX, y: touch.clientY };
    };

    const handleSidebarTouchEnd = (e: React.TouchEvent) => {
        if (!isMobile || !mobileSidebarOpen || !sidebarTouchStart.current) return;
        const touch = e.changedTouches[0];
        const dx = touch.clientX - sidebarTouchStart.current.x;
        const dy = touch.clientY - sidebarTouchStart.current.y;

        // Balayage vers la droite (dx > 0) → ferme
        if (Math.abs(dx) > 60 && Math.abs(dy) < 30 && dx > 0) {
            setMobileSidebarOpen(false);
        }
        sidebarTouchStart.current = null;
    };

    const handleBack = () => {
        const undone = undoLastAction();
        if (!undone) {
            navigate(-1);
        }
    };
    // --------------------------------------------------------

    return (
        <div
            className={`main-layout ${className}`.trim()}
            onTouchStart={handleMainTouchStart}
            onTouchEnd={handleMainTouchEnd}
            style={{ touchAction: 'pan-y' }}
        >
            {/* Bouton hamburger (mobile uniquement) */}
            {isMobile && !mobileSidebarOpen && (
                <button
                    className="main-layout__floating-toggle-left"
                    onClick={() => setMobileSidebarOpen(true)}
                    aria-label="Ouvrir le menu"
                    title="Ouvrir le menu"
                >
                    <MenuIcon />
                </button>
            )}

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
                    <aside
                        className={`main-layout__sidebar-left${isMobile && mobileSidebarOpen ? ' main-layout__sidebar-left--open' : ''}`}
                        onTouchStart={handleSidebarTouchStart}
                        onTouchEnd={handleSidebarTouchEnd}
                        style={{ touchAction: 'pan-y' }}
                    >
                        {sidebarContent || (
                            <Sidebar
                                groups={menuConfig}
                                userPermissions={permissions}
                                collapsible={!isMobile}
                                defaultCollapsed={isCompact && !isMobile}
                                activeRoute={location.pathname}
                                mobileOpen={isMobile ? mobileSidebarOpen : undefined}
                                onMobileClose={() => setMobileSidebarOpen(false)}
                                onItemClick={(item) => {
                                    if (isMobile && !('children' in item && item.children)) {
                                        setMobileSidebarOpen(false);
                                    }
                                    if ('route' in item && item.route) {
                                        navigate(item.route);
                                    }
                                }}
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
                                                onClick: () => navigate('/profile'),
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
                    {showHeader && <header className="main-layout__header" />}

                    <div className="main-layout__body">
                        <main className="main-layout__content">
                            {!mobileSidebarOpen && (
                                <button
                                    className="main-layout__back-button"
                                    onClick={handleBack}
                                    aria-label="Retour à la page précédente"
                                    title="Retour"
                                >
                                    <BackIcon />
                                </button>
                            )}
                            <div className="main-layout__page-content">
                                {children ?? <Outlet />}
                            </div>
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

                    {showFooter && <footer className="main-layout__footer" />}
                </div>
            </div>
        </div>
    );
}
