import React from 'react';
import { useCard, UseCardProps } from '../../../hook-components/UI/Card';

export interface CardProps extends UseCardProps {
    header?: React.ReactNode;
    footer?: React.ReactNode;
    children: React.ReactNode;
}

export function Card({
                         variant = 'default',
                         padding = 'medium',
                         interactive = false,
                         fullWidth = true,
                         className,
                         header,
                         footer,
                         children,
                     }: CardProps) {
    const { classes } = useCard({ variant, padding, interactive, fullWidth, className });

    return (
        <div className={classes}>
            {header && <div className="card__header">{header}</div>}
            <div className="card__body">{children}</div>
            {footer && <div className="card__footer">{footer}</div>}
        </div>
    );
}
