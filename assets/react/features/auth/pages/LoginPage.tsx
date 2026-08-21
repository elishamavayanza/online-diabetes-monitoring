import React from 'react';
import { LoginForm } from '@/react/features/auth';
import logo from '@/images/logo.png';
import { Card } from "@/react/components/UI/Card";
import { useIsMobile } from '@/react/hooks/useIsMobile';

export function LoginPage() {
    const isMobile = useIsMobile();

    return (
        <div className="login-page">
            <Card
                variant="elevated"
                padding={isMobile ? 'small' : 'large'}
                className="login-page__card"
            >
                <div className="login-page__header">
                    <img
                        src={logo}
                        alt="OnlineDIAB"
                        className="login-page__logo"
                        style={{
                            height: isMobile ? 54 : 85,
                            marginBottom: isMobile ? '0.5rem' : '0.5rem',
                        }}
                    />
                    <h1
                        className="login-page__title"
                        style={{ fontSize: isMobile ? '1.6rem' : '2.25rem' }}
                    >
                        OnlineDIAB
                    </h1>
                    <p
                        className="login-page__subtitle"
                        style={{ fontSize: isMobile ? '0.9rem' : '1.05rem' }}
                    >
                        Connectez-vous à votre espace
                    </p>
                </div>
                <LoginForm />
            </Card>
        </div>
    );
}
