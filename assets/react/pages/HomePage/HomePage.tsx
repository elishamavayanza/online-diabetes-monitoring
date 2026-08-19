import React, {useEffect, useMemo, useState} from 'react';
import styles from '../../../styles/pages/HomePage/_homePage.module.scss';
import {
    IconActivity,
    IconClipboard,
    IconUsers,
    IconMessageCircle,
    IconBell,
} from './icons';

const HomePage: React.FC = () => {
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const [windowWidth, setWindowWidth] = useState(window.innerWidth);

    const toggleMenu = () => {
        setIsMenuOpen(!isMenuOpen);
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
                    <a href="#" className={styles.logo}>
                        <img src="../../../images/logo.png" alt="Logo DiabCare" className={styles.logoImage} />
                        <span>DiabCare</span>
                    </a>

                    {/* Navigation desktop (visible sur écrans larges) */}
                    <nav className={styles.nav}>
                        <a href="#about">À propos</a>
                        <a href="#features">Fonctionnalités</a>
                        <a href="#users">Pour qui ?</a>
                        <button className={styles.ctaButton}>Se connecter</button>
                    </nav>

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

                {/* Menu mobile (affiché uniquement si isMenuOpen est true) */}
                {isMenuOpen && (
                    <div className={styles.mobileMenu}>
                        <a href="#about" onClick={toggleMenu}>À propos</a>
                        <a href="#features" onClick={toggleMenu}>Fonctionnalités</a>
                        <a href="#users" onClick={toggleMenu}>Pour qui ?</a>
                        <button className={styles.ctaButton} onClick={toggleMenu}>Se connecter</button>
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
                        Mieux suivre le diabète.<br />
                        Mieux accompagner chaque patient.
                    </h1>
                    <p>
                        DiabCare facilite le suivi quotidien des personnes vivant avec le diabète
                        et favorise une meilleure collaboration entre patients et professionnels de santé.
                    </p>
                    <button className={styles.primaryCta}>Se connecter</button>
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
                    <h2>Pourquoi DiabCare existe ?</h2>
                    <p>
                        Le suivi du diabète nécessite une attention régulière et une bonne coordination
                        entre le patient et les professionnels qui l'accompagnent.
                    </p>
                    <p>
                        DiabCare propose un espace centralisé permettant de réunir les informations
                        importantes du suivi médical afin de faciliter l'accompagnement et la prise de décision.
                    </p>
                </div>
            </section>

            {/* ===== FEATURES ===== */}
            <section id="features" className={styles.features}>
                <div className={styles.featuresInner}>
                    <h2>Ce que DiabCare permet</h2>
                    <div className={styles.cardsGrid}>
                        <div className={styles.card}>
                            <IconActivity />
                            <h3>Suivi de santé</h3>
                            <p>Suivre les principaux paramètres de santé et leur évolution au fil du temps.</p>
                        </div>
                        <div className={styles.card}>
                            <IconClipboard />
                            <h3>Traitements</h3>
                            <p>Retrouver les prescriptions et les traitements associés au parcours du patient.</p>
                        </div>
                        <div className={styles.card}>
                            <IconUsers />
                            <h3>Accompagnement médical</h3>
                            <p>Permettre aux professionnels de santé de mieux suivre leurs patients.</p>
                        </div>
                        <div className={styles.card}>
                            <IconMessageCircle />
                            <h3>Communication</h3>
                            <p>Faciliter les échanges entre patients et professionnels de santé.</p>
                        </div>
                        <div className={styles.card}>
                            <IconBell />
                            <h3>Rappels et événements</h3>
                            <p>Aider à organiser les différents événements liés au suivi médical.</p>
                        </div>
                    </div>
                </div>
            </section>

            {/* ===== FOR WHOM ===== */}
            <section id="users" className={styles.users}>
                <div className={styles.usersInner}>
                    <h2>Pour qui ?</h2>
                    <div className={styles.userBlocks}>
                        <div>
                            <h3>Patients</h3>
                            <p>Un suivi plus clair de leur santé, de leurs traitements et de leur évolution.</p>
                        </div>
                        <div>
                            <h3>Professionnels de santé</h3>
                            <p>Une meilleure visibilité sur les informations nécessaires au suivi de leurs patients.</p>
                        </div>
                        <div>
                            <h3>Structures de santé</h3>
                            <p>Une organisation centralisée des utilisateurs et du suivi médical.</p>
                        </div>
                    </div>
                </div>
            </section>

            {/* ===== FINAL CTA ===== */}
            <section className={styles.finalCta}>
                <div className={styles.finalCtaInner}>
                    <h2>Un suivi plus simple. <br />Une meilleure coordination.</h2>
                    <p>Découvrez DiabCare et son approche du suivi du diabète.</p>
                    <button className={styles.primaryCta}>Se connecter</button>
                </div>
            </section>

            {/* ===== FOOTER ===== */}
            <footer className={styles.footer}>
                <div className={styles.footerInner}>
                    <div className={styles.footerBrand}>
                        <strong>DiabCare</strong>
                        <p>Une plateforme pensée pour faciliter le suivi et l'accompagnement des personnes vivant avec le diabète.</p>
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
                            <a href="#">Se connecter</a>
                        </div>
                    </div>
                </div>
                <div className={styles.footerBottom}>
                    <p>© 2026 DiabCare — Projet académique et éducatif.</p>
                </div>
            </footer>
        </div>
    );
};

export default HomePage;
