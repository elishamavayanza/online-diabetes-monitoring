import React from 'react';
import './AuthLayout.scss';

interface AuthLayoutProps {
    children: React.ReactNode;
}

export default function AuthLayout({ children }: AuthLayoutProps) {
    return <div className="auth-layout">{children}</div>;
}
