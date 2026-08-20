import React, { forwardRef } from 'react';
import { useSwitch, UseSwitchProps } from '../../../hook-components/Forms/Switch';

export interface SwitchProps extends React.InputHTMLAttributes<HTMLInputElement>, UseSwitchProps {
    label?: React.ReactNode;
}

export const Switch = forwardRef<HTMLInputElement, SwitchProps>(
    (
        {
            variant = 'default',
            fieldSize = 'medium',
            disabled = false,
            className,
            label,
            ...rest
        },
        ref
    ) => {
        const { classes } = useSwitch({ variant, fieldSize, disabled, className });

        return (
            <label className={`${classes}__wrapper`}>
                <input
                    ref={ref}
                    type="checkbox"
                    className={classes}
                    disabled={disabled}
                    {...rest}
                />
                <span className="switch-field__track" aria-hidden="true">
          <span className="switch-field__thumb" />
        </span>
                {label && <span className="switch-field__label">{label}</span>}
            </label>
        );
    }
);

Switch.displayName = 'Switch';
