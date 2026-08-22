import React from 'react';
import { Header } from '@/react/components/Navigation/Header';
import { Footer } from '@/react/components/Navigation/Footer';

interface AppLayoutProps {
    children: React.ReactNode;
}

export default function AppLayout({ children }: AppLayoutProps) {
    // Exemple de navigation, à adapter
    const navItems = [
        { id: 'dashboard', label: 'Dashboard', href: '/' },
        { id: 'patients', label: 'Patients', href: '/patients' },
        { id: 'logout', label: 'Déconnexion', href: '/logout' },
    ];

    return (
        <div className="app-layout">
            <Header logo="DiabCare" navItems={navItems} />
            <main className="app-layout__main">{children}</main>
            <Footer brand="DiabCare" />
        </div>
    );
}
