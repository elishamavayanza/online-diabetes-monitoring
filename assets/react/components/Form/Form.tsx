import React, { forwardRef } from 'react';
import { useForm, UseFormProps } from '../../hook-components/Form';

export interface FormProps extends React.FormHTMLAttributes<HTMLFormElement>, UseFormProps {}

export const Form = forwardRef<HTMLFormElement, FormProps>(
    (
        {
            layout = 'vertical',
            gap = 'medium',
            fullWidth = false,
            className,
            children,
            ...rest
        },
        ref
    ) => {
        const { classes } = useForm({
            layout,
            gap,
            fullWidth,
            className,
        });

        return (
            <form ref={ref} className={classes} {...rest}>
                {children}
            </form>
        );
    }
);

Form.displayName = 'Form';
