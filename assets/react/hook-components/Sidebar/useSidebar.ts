import { useState, useMemo } from 'react';

export interface SidebarSubItem {
    id: string;
    label: React.ReactNode;
    href?: string;
    icon?: React.ReactNode;
    active?: boolean;
}

export interface SidebarItem {
    id: string;
    label: React.ReactNode;
    icon?: React.ReactNode;
    href?: string;
    active?: boolean;
    disabled?: boolean;
    children?: SidebarSubItem[];
}

export interface UseSidebarProps {
    items: SidebarItem[];
    variant?: 'default' | 'dark' | 'light';
    collapsible?: boolean;
    defaultCollapsed?: boolean;
    width?: string;
    activeId?: string;
    onItemClick?: (item: SidebarItem | SidebarSubItem) => void;
    className?: string;
}

export function useSidebar({
                               items,
                               variant = 'default',
                               collapsible = false,
                               defaultCollapsed = false,
                               width = '280px',
                               activeId,
                               onItemClick,
                               className = '',
                           }: UseSidebarProps) {
    const [isCollapsed, setIsCollapsed] = useState(defaultCollapsed);
    const [openSections, setOpenSections] = useState<Record<string, boolean>>({});

    const toggleCollapse = () => setIsCollapsed((prev) => !prev);

    const toggleSection = (sectionId: string) => {
        setOpenSections((prev) => ({
            ...prev,
            [sectionId]: !prev[sectionId],
        }));
    };

    const classes = useMemo(() => {
        const base = 'sidebar';
        const variantClass = `sidebar--${variant}`;
        const collapsedClass = collapsible && isCollapsed ? 'sidebar--collapsed' : '';
        return [base, variantClass, collapsedClass, className].filter(Boolean).join(' ');
    }, [variant, collapsible, isCollapsed, className]);

    const style = useMemo(() => {
        return { width: collapsible && isCollapsed ? '70px' : width };
    }, [collapsible, isCollapsed, width]);

    return {
        classes,
        style,
        isCollapsed,
        toggleCollapse,
        openSections,
        toggleSection,
        activeId,
    };
}
