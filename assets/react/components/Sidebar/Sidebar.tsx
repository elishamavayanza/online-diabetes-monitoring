import React from 'react';
import { useSidebar, UseSidebarProps, SidebarItem, SidebarSubItem } from '../../hook-components/Sidebar';

const CollapseIcon = () => (
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2">
        <polyline points="15 18 9 12 15 6" />
    </svg>
);

const ExpandIcon = () => (
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2">
        <polyline points="9 18 15 12 9 6" />
    </svg>
);

export interface SidebarProps extends UseSidebarProps {
    header?: React.ReactNode;
    footer?: React.ReactNode;
}

export function Sidebar({
                            items,
                            variant,
                            collapsible,
                            defaultCollapsed,
                            width,
                            activeId,
                            onItemClick,
                            className,
                            header,
                            footer,
                        }: SidebarProps) {
    const {
        classes,
        style,
        isCollapsed,
        toggleCollapse,
        openSections,
        toggleSection,
    } = useSidebar({
        items,
        variant,
        collapsible,
        defaultCollapsed,
        width,
        activeId,
        onItemClick,
        className,
    });

    const handleItemClick = (item: SidebarItem | SidebarSubItem) => {
        onItemClick?.(item);
        if ('children' in item && item.children) {
            toggleSection(item.id);
        }
    };

    return (
        <aside className={classes} style={style}>
            {collapsible && (
                <button className="sidebar__collapse" onClick={toggleCollapse} aria-label="Réduire/agrandir">
                    {isCollapsed ? <ExpandIcon /> : <CollapseIcon />}
                </button>
            )}
            {header && <div className="sidebar__header">{header}</div>}
            <nav className="sidebar__nav" aria-label="Navigation latérale">
                {items.map((item) => (
                    <div key={item.id} className="sidebar__group">
                        <div
                            className={`sidebar__item ${item.active || item.id === activeId ? 'sidebar__item--active' : ''} ${item.disabled ? 'sidebar__item--disabled' : ''}`}
                            onClick={() => handleItemClick(item)}
                            role="button"
                            tabIndex={item.disabled ? -1 : 0}
                        >
                            {item.icon && <span className="sidebar__icon">{item.icon}</span>}
                            {!isCollapsed && <span className="sidebar__label">{item.label}</span>}
                            {!isCollapsed && item.children && (
                                <span className="sidebar__arrow">
                  {openSections[item.id] ? '▾' : '▸'}
                </span>
                            )}
                        </div>
                        {!isCollapsed && item.children && openSections[item.id] && (
                            <div className="sidebar__subitems">
                                {item.children.map((child) => (
                                    <div
                                        key={child.id}
                                        className={`sidebar__subitem ${child.active || child.id === activeId ? 'sidebar__subitem--active' : ''}`}
                                        onClick={() => handleItemClick(child)}
                                        role="button"
                                        tabIndex={0}
                                    >
                                        {child.icon && <span className="sidebar__icon">{child.icon}</span>}
                                        <span className="sidebar__label">{child.label}</span>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                ))}
            </nav>
            {footer && <div className="sidebar__footer">{footer}</div>}
        </aside>
    );
}
