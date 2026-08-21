import React from 'react';
import { LoginForm } from '@/react/features/auth';
import logo from '@/images/logo.png';
import {Card} from "@/react/components/UI/Card";

export function LoginPage() {
    return (
        <div className="login-page">
            <div className="login-page__header">
                <img src={logo} alt="DiabCare" className="login-page__logo" />
                <h1 className="login-page__title">Connexion</h1>
                <p className="login-page__subtitle">Accédez à votre espace de suivi</p>
            </div>
            <Card variant="elevated" padding="large" className="login-page__card">
                <LoginForm />
            </Card>
        </div>
    );
}
