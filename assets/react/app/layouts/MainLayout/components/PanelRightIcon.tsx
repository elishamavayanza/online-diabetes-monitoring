// src/react/app/layouts/MainLayout/components/PanelRightIcon.tsx
import React from 'react';

interface PanelRightIconProps {
    open: boolean;
    size?: number;
}

export function PanelRightIcon({ open, size = 18 }: PanelRightIconProps) {
    return (
        <svg
            width={size}
            height={size}
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
        >
            {open ? (
                <polyline points="15 18 9 12 15 6" />
            ) : (
                <polyline points="9 18 15 12 9 6" />
            )}
        </svg>
    );
}
