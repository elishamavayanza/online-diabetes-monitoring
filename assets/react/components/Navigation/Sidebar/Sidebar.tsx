import React from 'react';
import { useSidebar, UseSidebarProps, SidebarItem, SidebarSubItem, SidebarGroup } from '../../../hook-components/Navigation/Sidebar';

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
                            groups,
                            variant,
                            collapsible,
                            defaultCollapsed,
                            width,
                            activeId,
                            onItemClick,
                            className,
                            header,
                            footer,
                            userPermissions,
                        }: SidebarProps) {
    const {
        classes,
        isCollapsed,
        toggleCollapse,
        openSections,
        toggleSection,
        filteredItems,
    } = useSidebar({
        items,
        groups,
        variant,
        collapsible,
        defaultCollapsed,
        width,
        activeId,
        onItemClick,
        className,
        userPermissions,
    });

    const handleItemClick = (item: SidebarItem | SidebarSubItem) => {
        onItemClick?.(item);
        if ('children' in item && item.children) {
            toggleSection(item.id);
        }
    };

    const renderItem = (item: SidebarItem, isCollapsed: boolean) => (
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
    );

    const renderContent = () => {
        if (groups) {
            return (filteredItems as SidebarGroup[]).map((group) => (
                <div key={group.id} className="sidebar__group-section">
                    {!isCollapsed && <div className="sidebar__group-label">{group.label}</div>}
                    {group.items.map((item) => renderItem(item as SidebarItem, isCollapsed))}
                </div>
            ));
        }
        return (filteredItems as SidebarItem[]).map((item) => renderItem(item, isCollapsed));
    };

    return (
        <aside className={classes}>
            <div className="sidebar__header">
                <div
                    className="sidebar__header-brand"
                    onClick={toggleCollapse}
                    role="button"
                    tabIndex={0}
                    onKeyDown={(e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            toggleCollapse();
                        }
                    }}
                    title={isCollapsed ? 'Déplier le menu' : 'Replier le menu'}
                >
                    {header}
                </div>
                {collapsible && !isCollapsed && (
                    <button
                        className="sidebar__collapse"
                        onClick={toggleCollapse}
                        aria-label="Replier le menu"
                        title="Replier"
                    >
                        <CollapseIcon />
                    </button>
                )}
            </div>

            <nav className="sidebar__nav" aria-label="Navigation latérale">
                {renderContent()}
            </nav>

            {footer && <div className="sidebar__footer">{footer}</div>}
        </aside>
    );
}
