import React, { useEffect, useState } from 'react';
import { LoginForm } from '@/react/features/auth';
import logo from '@/images/logo.png';
import { Card } from "@/react/components/UI/Card";
import { Alert } from '@/react/components/UI/Alert';
import { useIsMobile } from '@/react/hooks/useIsMobile';
import { useSystemSettings } from '@/react/hooks/useSystemSettings';

const SESSION_EXPIRED_KEY = 'diabcare-session-expired';

export function LoginPage() {
    const isMobile = useIsMobile();
    const [showSessionExpired, setShowSessionExpired] = useState(false);
    const { settings } = useSystemSettings();

    const systemName = settings?.systemName || 'OnlineDIAB';
    const brandLogo = settings?.logoUrl || logo;

    useEffect(() => {
        try {
            if (sessionStorage.getItem(SESSION_EXPIRED_KEY) === '1') {
                setShowSessionExpired(true);
                sessionStorage.removeItem(SESSION_EXPIRED_KEY);
            }
        } catch {
            // stockage indisponible : pas de bannière
        }
    }, []);

    return (
        <div className="login-page">
            {showSessionExpired && (
                <Alert
                    variant="warning"
                    className="login-page__session-expired"
                    onClose={() => setShowSessionExpired(false)}
                >
                    Votre session a expiré. Veuillez vous reconnecter pour continuer.
                </Alert>
            )}
            <Card
                variant="elevated"
                padding={isMobile ? 'small' : 'large'}
                className="login-page__card"
            >
                <div className="login-page__header">
                    <img
                        src={brandLogo}
                        alt={systemName}
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
                        {systemName}
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
