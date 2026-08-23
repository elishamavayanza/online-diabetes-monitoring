import React, { useState } from 'react';
import { MainLayout } from '@/react/app/layouts/MainLayout/MainLayout';

export function DashboardPage() {
    const [showRightSidebar, setShowRightSidebar] = useState(true);

    return (
        <MainLayout
            showFooter={true}
            showRightSidebar={showRightSidebar}
            rightSidebarProps={{
                title: "Statistiques",
                header: <div>📊 Détails du tableau de bord</div>,

                minWidth: 250,
                maxWidth: 500,
                onResize: (width) => {
                    console.log('Nouvelle largeur:', width);
                }
            }}
            rightSidebarContent={
                <div>
                    <h3>Informations rapides</h3>
                    <ul>
                        <li>🔔 5 nouvelles notifications</li>
                        <li>📈 Tendance: +12%</li>
                        <li>👥 128 utilisateurs actifs</li>
                        <li>📊 Taux de conversion: 4.5%</li>
                    </ul>
                </div>
            }
        >
            <div style={{ maxWidth: '1200px', margin: '0 auto' }}>
                <h1>Dashboard</h1>
                <p>Bienvenue dans votre espace personnel.</p>

                {/* Exemple de contenu */}
                <div style={{
                    display: 'grid',
                    gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))',
                    gap: '1rem',
                    marginTop: '2rem'
                }}>
                    <div style={{ padding: '1.5rem', background: '#fff', borderRadius: '8px', boxShadow: '0 2px 4px rgba(0,0,0,0.1)' }}>
                        <h3>Statistiques</h3>
                        <p>+25% cette semaine</p>
                    </div>
                    <div style={{ padding: '1.5rem', background: '#fff', borderRadius: '8px', boxShadow: '0 2px 4px rgba(0,0,0,0.1)' }}>
                        <h3>Utilisateurs</h3>
                        <p>128 actifs</p>
                    </div>
                    <div style={{ padding: '1.5rem', background: '#fff', borderRadius: '8px', boxShadow: '0 2px 4px rgba(0,0,0,0.1)' }}>
                        <h3>Revenus</h3>
                        <p>12 450 €</p>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
