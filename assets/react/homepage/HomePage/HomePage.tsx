import React, { useEffect, useMemo, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import styles from '../../../styles/pages/HomePage/_homePage.module.scss';
import {
    IconActivity,
    IconClipboard,
    IconUsers,
    IconMessageCircle,
    IconBell,
} from './icons';
import { useAuth } from "@/react/app/providers/AuthProvider";
import { useTheme } from '@/react/hooks/ThemeProvider';
import { useSystemSettings } from '@/react/hooks/useSystemSettings';
import { SettingsItem } from '@/react/features/root/settings/types';

const SunIcon = () => (
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <circle cx="12" cy="12" r="4" />
        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" />
    </svg>
);

const MoonIcon = () => (
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
    </svg>
);

const DEFAULT_FEATURES: SettingsItem[] = [
    { title: 'Suivi de santé', description: 'Suivre les principaux paramètres de santé et leur évolution au fil du temps.' },
    { title: 'Traitements', description: 'Retrouver les prescriptions et les traitements associés au parcours du patient.' },
    { title: 'Accompagnement médical', description: 'Permettre aux professionnels de santé de mieux suivre leurs patients.' },
    { title: 'Communication', description: 'Faciliter les échanges entre patients et professionnels de santé.' },
    { title: 'Rappels et événements', description: 'Aider à organiser les différents événements liés au suivi médical.' },
];

const DEFAULT_USERS: SettingsItem[] = [
    { title: 'Patients', description: 'Un suivi plus clair de leur santé, de leurs traitements et de leur évolution.' },
    { title: 'Professionnels de santé', description: 'Une meilleure visibilité sur les informations nécessaires au suivi de leurs patients.' },
    { title: 'Structures de santé', description: 'Une organisation centralisée des utilisateurs et du suivi médical.' },
];

const FEATURE_ICONS = [IconActivity, IconClipboard, IconUsers, IconMessageCircle, IconBell];

/** Rendu d'un texte multi-lignes : chaque saut de ligne devient un <br />. */
function Lines({ text, fallback }: { text?: string; fallback: string }) {
    const value = text && text.trim() ? text : fallback;
    return (
        <>
            {value.split('\n').map((line, index, all) => (
                <span key={index}>
                    {line}
                    {index < all.length - 1 && <br />}
                </span>
            ))}
        </>
    );
}

function Paragraphs({ text, fallback }: { text?: string; fallback: string }) {
    const value = text && text.trim() ? text : fallback;
    return (
        <>
            {value
                .split(/\n+/)
                .filter((item) => item.trim())
                .map((paragraph, index) => (
                    <p key={index}>{paragraph}</p>
                ))}
        </>
    );
}

const HomePage: React.FC = () => {
    const navigate = useNavigate();
    const { isAuthenticated } = useAuth();
    const { theme, toggleTheme } = useTheme();
    const { settings } = useSystemSettings();
    const isDark = theme === 'dark';
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const [windowWidth, setWindowWidth] = useState(window.innerWidth);

    const systemName = settings?.systemName || 'OnlineDIAB';
    const logoUrl = settings?.logoUrl || '../../../images/logo.png';
    const features = settings?.features?.length ? settings.features : DEFAULT_FEATURES;
    const users = settings?.users?.length ? settings.users : DEFAULT_USERS;

    const toggleMenu = () => {
        setIsMenuOpen(!isMenuOpen);
    };

    const goToLogin = () => {
        navigate(isAuthenticated ? '/app' : '/login');
    };

    useEffect(() => {
        const handleResize = () => setWindowWidth(window.innerWidth);
        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    const bubbles = useMemo(() => {
        const isMobile = windowWidth <= 768;
        const isSmallMobile = windowWidth <= 480;
        const bubbleCount = isSmallMobile ? 8 : isMobile ? 12 : 20;
        const minSize = isSmallMobile ? 15 : isMobile ? 20 : 20;
        const maxSize = isSmallMobile ? 35 : isMobile ? 50 : 80;

        return Array.from({ length: bubbleCount }, (_, i) => {
            const size = Math.random() * (maxSize - minSize) + minSize;
            // Déplacement aléatoire (en pixels) pour la trajectoire
            const tx = (Math.random() * 80 - 40).toFixed(1); // -40 à 40 px
            const ty = (Math.random() * 80 - 40).toFixed(1);
            return {
                id: i,
                width: size,
                height: size,
                top: `${Math.random() * 100}%`,
                left: `${Math.random() * 100}%`,
                animationDuration: `${Math.random() * 4 + 3}s`, // 3 à 7 s
                animationDelay: `${Math.random() * 3}s`,
                backgroundColor: `rgba(255, 255, 255, ${Math.random() * 0.3 + 0.1})`,
                // Variables CSS pour la trajectoire
                '--tx': `${tx}px`,
                '--ty': `${ty}px`,
            };
        });
    }, [windowWidth]);

    return (
        <div className={styles.page}>
            {/* ===== HEADER ===== */}
            <header className={styles.header}>
                <div className={styles.headerInner}>
                    <a
                        href="#"
                        className={styles.logo}
                        onClick={(e) => {
                            e.preventDefault();
                            goToLogin();
                        }}
                    >
                        <img src={logoUrl} alt={`Logo ${systemName}`} className={styles.logoImage} />
                        <span>{systemName}</span>
                    </a>

                    <div className={styles.headerRight}>
                        {/* Navigation desktop (visible sur écrans larges) */}
                        <nav className={styles.nav}>
                            <a href="#about">À propos</a>
                            <a href="#features">Fonctionnalités</a>
                            <a href="#users">Pour qui ?</a>
                            <button className={styles.ctaButton} onClick={goToLogin}>Se connecter</button>
                        </nav>

                        <div className={styles.headerActions}>
                            {/* Bascule de thème (sombre / clair) */}
                            <button
                                type="button"
                                className={styles.themeToggle}
                                onClick={toggleTheme}
                                aria-label={isDark ? 'Activer le thème clair' : 'Activer le thème sombre'}
                                title={isDark ? 'Thème clair' : 'Thème sombre'}
                            >
                                {isDark ? <SunIcon /> : <MoonIcon />}
                                <span className={styles.themeToggleLabel}>{isDark ? 'Clair' : 'Sombre'}</span>
                            </button>

                            {/* Bouton hamburger (visible sur mobile) */}
                            <button
                                className={`${styles.menuToggle} ${isMenuOpen ? styles.active : ''}`}
                                onClick={toggleMenu}
                                aria-label="Menu"
                                aria-expanded={isMenuOpen}
                            >
                                <span></span>
                                <span></span>
                                <span></span>
                            </button>
                        </div>
                    </div>
                </div>

                {/* Menu mobile (affiché uniquement si isMenuOpen est true) */}
                {isMenuOpen && (
                    <div className={styles.mobileMenu}>
                        <a href="#about" onClick={toggleMenu}>À propos</a>
                        <a href="#features" onClick={toggleMenu}>Fonctionnalités</a>
                        <a href="#users" onClick={toggleMenu}>Pour qui ?</a>
                        <button className={styles.ctaButton} onClick={() => { toggleMenu(); goToLogin(); }}>Se connecter</button>
                    </div>
                )}
            </header>

            {/* ===== HERO ===== */}
            <section className={styles.hero}>
                <div className={styles.bubbles}>
                    {bubbles.map((bubble) => (
                        <span
                            key={bubble.id}
                            className={styles.bubble}
                            style={{
                                width: `${bubble.width}px`,
                                height: `${bubble.height}px`,
                                top: bubble.top,
                                left: bubble.left,
                                animationDuration: bubble.animationDuration,
                                animationDelay: bubble.animationDelay,
                                backgroundColor: bubble.backgroundColor,
                                // Passer les variables de trajectoire
                                '--tx': bubble['--tx'],
                                '--ty': bubble['--ty'],
                            } as React.CSSProperties}
                        ></span>
                    ))}
                </div>
                <div className={styles.heroContent}>
                    <h1>
                        <Lines
                            text={settings?.heroTitle}
                            fallback={'Mieux suivre le diabète.\nMieux accompagner chaque patient.'}
                        />
                    </h1>
                    <p>
                        <Lines
                            text={settings?.heroSubtitle}
                            fallback={
                                'OnlineDIAB facilite le suivi quotidien des personnes vivant avec le diabète ' +
                                'et favorise une meilleure collaboration entre patients et professionnels de santé.'
                            }
                        />
                    </p>
                    <button className={styles.primaryCta} onClick={goToLogin}>Se connecter</button>
                </div>
                <div className={styles.heroIllustration}>
                    <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                        {/* Cercles de fond avec dégradé */}
                        <defs>
                            <linearGradient id="gradPrimary" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stopColor="#2C7A7B" stopOpacity={0.2} />
                                <stop offset="100%" stopColor="#76B8B8" stopOpacity={0.1} />
                            </linearGradient>
                            <linearGradient id="gradSecondary" x1="100%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stopColor="#76B8B8" stopOpacity={0.2} />
                                <stop offset="100%" stopColor="#2C7A7B" stopOpacity={0.1} />
                            </linearGradient>
                        </defs>

                        {/* Deux sphères translucides */}
                        <circle cx="120" cy="150" r="80" fill="url(#gradPrimary)" />
                        <circle cx="280" cy="150" r="80" fill="url(#gradSecondary)" />

                        {/* Connexion */}
                        <path
                            d="M120 150 Q200 80 280 150 Q200 220 120 150"
                            stroke="#2C7A7B"
                            strokeWidth={3}
                            strokeLinecap="round"
                            strokeDasharray="6 6"
                            fill="none"
                        />

                        {/* Points centraux */}
                        <circle cx="120" cy="150" r="10" fill="#2C7A7B" />
                        <circle cx="280" cy="150" r="10" fill="#76B8B8" />

                        {/* Dossier médical stylisé */}
                        <g transform="translate(160,125)">
                            <rect width="80" height="50" rx="10" fill="white" stroke="#2C7A7B" strokeWidth={2} />
                            {/* Lignes de données */}
                            <line x1="15" y1="15" x2="65" y2="15" stroke="#2C7A7B" strokeWidth={3} strokeLinecap="round" />
                            <line x1="15" y1="25" x2="50" y2="25" stroke="#76B8B8" strokeWidth={3} strokeLinecap="round" />
                            <line x1="15" y1="35" x2="35" y2="35" stroke="#2C7A7B" strokeWidth={3} strokeLinecap="round" />
                            {/* Petite icône croix médicale */}
                            <path d="M65 20 L75 20 M70 15 L70 25" stroke="#F4A261" strokeWidth={3} strokeLinecap="round" />
                        </g>
                    </svg>
                </div>
            </section>

            {/* ===== ABOUT ===== */}
            <section id="about" className={styles.about}>
                <div className={styles.aboutInner}>
                    <h2>
                        <Lines text={settings?.aboutTitle} fallback={'Pourquoi OnlineDIAB existe ?'} />
                    </h2>
                    <Paragraphs
                        text={settings?.aboutContent}
                        fallback={
                            'Le suivi du diabète nécessite une attention régulière et une bonne coordination ' +
                            'entre le patient et les professionnels qui l\'accompagnent.\n' +
                            'OnlineDIAB propose un espace centralisé permettant de réunir les informations ' +
                            'importantes du suivi médical afin de faciliter l\'accompagnement et la prise de décision.'
                        }
                    />
                </div>
            </section>

            {/* ===== FEATURES ===== */}
            <section id="features" className={styles.features}>
                <div className={styles.featuresInner}>
                    <h2>
                        <Lines text={settings?.featuresTitle} fallback={'Ce que OnlineDIAB permet'} />
                    </h2>
                    <div className={styles.cardsGrid}>
                        {features.map((feature, index) => {
                            const Icon = FEATURE_ICONS[index % FEATURE_ICONS.length];
                            return (
                                <div className={styles.card} key={index}>
                                    <Icon />
                                    <h3>{feature.title}</h3>
                                    <p>{feature.description}</p>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* ===== FOR WHOM ===== */}
            <section id="users" className={styles.users}>
                <div className={styles.usersInner}>
                    <h2>
                        <Lines text={settings?.usersTitle} fallback={'Pour qui ?'} />
                    </h2>
                    <div className={styles.userBlocks}>
                        {users.map((user, index) => (
                            <div key={index}>
                                <h3>{user.title}</h3>
                                <p>{user.description}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ===== FINAL CTA ===== */}
            <section className={styles.finalCta}>
                <div className={styles.finalCtaInner}>
                    <h2>
                        <Lines
                            text={settings?.ctaTitle}
                            fallback={'Un suivi plus simple.\nUne meilleure coordination.'}
                        />
                    </h2>
                    <p>
                        <Lines
                            text={settings?.ctaSubtitle}
                            fallback={'Découvrez OnlineDIAB et son approche du suivi du diabète.'}
                        />
                    </p>
                    <button className={styles.primaryCta} onClick={goToLogin}>Se connecter</button>
                </div>
            </section>

            {/* ===== FOOTER ===== */}
            <footer className={styles.footer}>
                <div className={styles.footerInner}>
                    <div className={styles.footerBrand}>
                        <strong>{systemName}</strong>
                        <p>
                            <Lines
                                text={settings?.footerTagline}
                                fallback={
                                    'Une plateforme pensée pour faciliter le suivi et l\'accompagnement des ' +
                                    'personnes vivant avec le diabète.'
                                }
                            />
                        </p>
                    </div>
                    <div className={styles.footerLinks}>
                        <div>
                            <h4>Navigation</h4>
                            <a href="#about">À propos</a>
                            <a href="#features">Fonctionnalités</a>
                            <a href="#users">Pour qui ?</a>
                        </div>
                        <div>
                            <h4>Compte</h4>
                            <a href="/login" onClick={(event) => { event.preventDefault(); goToLogin(); }}>Se connecter</a>
                        </div>
                    </div>
                    {/* Image/logo à droite */}
                    <div className={styles.footerLogo}>
                        <img src={logoUrl} alt={`Logo ${systemName}`} />
                    </div>
                </div>
                <div className={styles.footerBottom}>
                    <p>
                        <Lines
                            text={settings?.footerCopyright}
                            fallback={'© 2026 OnlineDIAB — Projet académique et éducatif.'}
                        />
                    </p>
                </div>
            </footer>
        </div>
    );
};

export default HomePage;