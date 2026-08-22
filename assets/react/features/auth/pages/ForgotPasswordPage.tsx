import React from 'react';
import { ForgotPasswordForm } from '../components/ForgotPasswordForm';
import logo from '@/images/logo.png';
import { Card } from '@/react/components/UI/Card';
import { useIsMobile } from '@/react/hooks/useIsMobile';

export function ForgotPasswordPage() {
    const isMobile = useIsMobile();

    return (
        <div className="forgot-password-page">
            <Card
                variant="elevated"
                padding={isMobile ? 'small' : 'large'}
                className="forgot-password-page__card"
            >
                <div className="forgot-password-page__header">
                    <img
                        src={logo}
                        alt="OnlineDIAB"
                        className="forgot-password-page__logo"
                        style={{
                            height: isMobile ? 54 : 85,
                            marginBottom: isMobile ? '0.5rem' : '0.5rem',
                        }}
                    />
                    <h1
                        className="forgot-password-page__title"
                        style={{ fontSize: isMobile ? '1.6rem' : '2.25rem' }}
                    >
                        Mot de passe oublié ?
                    </h1>
                    <p
                        className="forgot-password-page__subtitle"
                        style={{ fontSize: isMobile ? '0.9rem' : '1.05rem' }}
                    >
                        Entrez votre email pour recevoir un lien de réinitialisation.
                    </p>
                </div>
                <ForgotPasswordForm />
            </Card>
        </div>
    );
}
