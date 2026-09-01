// measurementIcons.tsx
import React from 'react';

interface IconProps {
    size?: number;
    className?: string;
}

const base = (size: number) => ({
    width: size,
    height: size,
    viewBox: '0 0 24 24',
    fill: 'none',
    stroke: 'currentColor',
    strokeWidth: 2,
    strokeLinecap: 'round' as const,
    strokeLinejoin: 'round' as const,
});

export const BloodGlucoseIcon = ({ size = 28, className }: IconProps) => (
    <svg {...base(size)} className={className} style={{ color: '#e74c3c' }}>
        <path d="M12 2v4" />
        <path d="M12 18v4" />
        <path d="M4.93 4.93l2.83 2.83" />
        <path d="M16.24 16.24l2.83 2.83" />
        <path d="M2 12h4" />
        <path d="M18 12h4" />
        <path d="M4.93 19.07l2.83-2.83" />
        <path d="M16.24 7.76l2.83-2.83" />
        <circle cx="12" cy="12" r="3" fill="currentColor" stroke="none" />
    </svg>
);

export const BloodPressureIcon = ({ size = 28, className }: IconProps) => (
    <svg {...base(size)} className={className} style={{ color: '#e67e22' }}>
        <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
    </svg>
);

export const Hba1cIcon = ({ size = 28, className }: IconProps) => (
    <svg {...base(size)} className={className} style={{ color: '#2980b9' }}>
        <line x1="4" y1="20" x2="20" y2="20" />
        <rect x="6" y="10" width="4" height="8" rx="1" />
        <rect x="12" y="6" width="4" height="12" rx="1" />
        <rect x="18" y="2" width="4" height="16" rx="1" />
    </svg>
);

export const WeightIcon = ({ size = 28, className }: IconProps) => (
    <svg {...base(size)} className={className} style={{ color: '#27ae60' }}>
        <path d="M12 2v20" />
        <path d="M6 6h12" />
        <path d="M8 10h8" />
        <path d="M8 14h8" />
    </svg>
);

export const PhysicalActivityIcon = ({ size = 28, className }: IconProps) => (
    <svg {...base(size)} className={className} style={{ color: '#8e44ad' }}>
        <circle cx="17" cy="4" r="2" />
        <path d="M7 20l3-5 3 2 3-4" />
        <path d="M7 20l-3 1" />
        <path d="M10 15l-2-2" />
    </svg>
);

export const LaboratoryIcon = ({ size = 28, className }: IconProps) => (
    <svg {...base(size)} className={className} style={{ color: '#16a085' }}>
        <path d="M9 3h6" />
        <path d="M10 3v7l-7 11h18L14 10V3" />
        <path d="M6.5 21h11" />
    </svg>
);
