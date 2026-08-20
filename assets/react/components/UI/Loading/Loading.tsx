import React from 'react';
import { Spinner } from '../Spinner';
import { UseSpinnerProps } from '../../../hook-components/UI/Spinner';

export interface LoadingProps extends UseSpinnerProps {
    text?: string;
}

export function Loading({ text = 'Chargement...', ...spinnerProps }: LoadingProps) {
    return (
        <div className="loading">
            <Spinner {...spinnerProps} />
            {text && <span className="loading__text">{text}</span>}
        </div>
    );
}
