import React from 'react';
import { LoginForm } from '@/react/features/auth';
import logo from '@/images/logo.png';
import { Card } from "@/react/components/UI/Card";

export function LoginPage() {
    return (
        <div className="login-page">
            <Card variant="elevated" padding="large" className="login-page__card">
                <div className="login-page__header">
                    <img src={logo} alt="DiabCare" className="login-page__logo" />
                    <h1 className="login-page__title">OnlineDIAB</h1>
                    <p className="login-page__subtitle"> Connectez-vous à votre espace</p>
                </div>
                <LoginForm />
            </Card>
        </div>
    );
}
