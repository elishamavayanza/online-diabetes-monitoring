import React from 'react';
import { useRightSidebar, UseRightSidebarProps } from '../../../hook-components/Navigation/RightSidebar';

const CollapseIcon = ({ collapsed }: { collapsed: boolean }) => (
    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2">
        {collapsed ? (
            <polyline points="9 18 15 12 9 6" />
        ) : (
            <polyline points="15 18 9 12 15 6" />
        )}
    </svg>
);

export interface RightSidebarProps extends UseRightSidebarProps {
    /** Contenu de la sidebar droite */
    children: React.ReactNode;
    /** Titre optionnel affiché en haut */
    title?: React.ReactNode;
    /** Élément de header personnalisé (au-dessus du titre) */
    header?: React.ReactNode;
    /** Élément de footer personnalisé */
    footer?: React.ReactNode;
    /** Callback lors du clic sur le bouton de repli */
    onToggle?: (collapsed: boolean) => void;
}

export function RightSidebar({
                                 children,
                                 title,
                                 header,
                                 footer,
                                 variant,
                                 size,
                                 collapsible,
                                 defaultCollapsed,
                                 className,
                                 onToggle,
                             }: RightSidebarProps) {
    const { classes, isCollapsed, toggleCollapse } = useRightSidebar({
        variant,
        size,
        collapsible,
        defaultCollapsed,
        className,
    });

    const handleToggle = () => {
        toggleCollapse();
        if (onToggle) onToggle(!isCollapsed);
    };

    return (
        <aside className={classes}>
            {collapsible && (
                <button
                    type="button"
                    className="right-sidebar__collapse"
                    onClick={handleToggle}
                    aria-label={isCollapsed ? 'Déplier la sidebar' : 'Replier la sidebar'}
                    aria-expanded={!isCollapsed}
                >
                    <CollapseIcon collapsed={isCollapsed} />
                </button>
            )}

            {header && <div className="right-sidebar__header">{header}</div>}

            {!isCollapsed && (
                <>
                    {title && <div className="right-sidebar__title">{title}</div>}
                    <div className="right-sidebar__content">{children}</div>
                    {footer && <div className="right-sidebar__footer">{footer}</div>}
                </>
            )}
        </aside>
    );
}
