import React, { createContext, useContext, useState, useCallback } from 'react';

export type ToastType = 'success' | 'error' | 'info' | 'warning';

export interface ToastOptions {
    id?: string;
    title?: string;
    message: string;
    type?: ToastType;
    duration?: number;
}

interface ToastContextValue {
    showToast: (options: ToastOptions) => void;
    hideToast: (id: string) => void;
}

const ToastContext = createContext<ToastContextValue | undefined>(undefined);

export const useToast = () => {
    const context = useContext(ToastContext);
    if (!context) {
        throw new Error('useToast must be used within a ToastProvider');
    }
    return context;
};

// Icônes SVG inline
const IconInfo = () => (
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2">
        <circle cx="12" cy="12" r="10" />
        <line x1="12" y1="16" x2="12" y2="12" />
        <line x1="12" y1="8" x2="12.01" y2="8" />
    </svg>
);
const IconSuccess = () => (
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
        <polyline points="22 4 12 14.01 9 11.01" />
    </svg>
);
const IconWarning = () => (
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2">
        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
        <line x1="12" y1="9" x2="12" y2="13" />
        <line x1="12" y1="17" x2="12.01" y2="17" />
    </svg>
);
const IconError = () => (
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2">
        <circle cx="12" cy="12" r="10" />
        <line x1="15" y1="9" x2="9" y2="15" />
        <line x1="9" y1="9" x2="15" y2="15" />
    </svg>
);

const variantIcon: Record<ToastType, React.ReactNode> = {
    info: <IconInfo />,
    success: <IconSuccess />,
    warning: <IconWarning />,
    error: <IconError />,
};

export const ToastProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const [toasts, setToasts] = useState<ToastOptions[]>([]);

    const hideToast = useCallback((id: string) => {
        setToasts((prev) => prev.filter((toast) => toast.id !== id));
    }, []);

    const showToast = useCallback((options: ToastOptions) => {
        const id = options.id ?? `toast-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
        const duration = options.duration ?? 5000;
        const toast = { ...options, id };
        setToasts((prev) => [...prev, toast]);

        setTimeout(() => {
            hideToast(id);
        }, duration);
    }, [hideToast]);

    return (
        <ToastContext.Provider value={{ showToast, hideToast }}>
            {children}
            <div className="toast-container toast-container--top-right">
                {toasts.map((toast) => (
                    <div key={toast.id} className={`toast toast--${toast.type}`} role="alert">
                        <div className="toast__icon" aria-hidden="true">
                            {variantIcon[toast.type!]}
                        </div>
                        <div className="toast__message">{toast.message}</div>
                        <button className="toast__close" onClick={() => hideToast(toast.id!)} aria-label="Fermer">
                            ×
                        </button>
                    </div>
                ))}
            </div>
        </ToastContext.Provider>
    );
};
