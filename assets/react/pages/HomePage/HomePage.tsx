import React from 'react';
import styles from '../../../styles/pages/HomePage/HomePage.module.scss';
import {
    IconActivity,
    IconClipboard,
    IconUsers,
    IconMessageCircle,
    IconBell,
} from './icons';

const HomePage: React.FC = () => {
    return (
        <div className={styles.page}>
            {/* ===== HEADER ===== */}
            <header className={styles.header}>
                <div className={styles.headerInner}>
                    <div className={styles.logo}>DiabCare</div>
                    <nav className={styles.nav}>
                        <a href="#about">À propos</a>
                        <a href="#features">Fonctionnalités</a>
                        <a href="#users">Pour qui ?</a>
                        <button className={styles.ctaButton}>Se connecter</button>
                    </nav>
                    <button className={styles.menuToggle} aria-label="Menu">
                        <span></span><span></span><span></span>
                    </button>
                </div>
            </header>

            {/* ===== HERO ===== */}
            <section className={styles.hero}>
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
                        <circle cx="120" cy="150" r="80" fill="var(--color-primary)" opacity="0.15" />
                        <circle cx="280" cy="150" r="80" fill="var(--color-secondary)" opacity="0.15" />
                        <path d="M120 150 Q200 80 280 150 Q200 220 120 150" stroke="var(--color-primary)" strokeWidth="2" fill="none" />
                        <circle cx="120" cy="150" r="12" fill="var(--color-primary)" />
                        <circle cx="280" cy="150" r="12" fill="var(--color-secondary)" />
                        <rect x="160" y="130" width="80" height="40" rx="6" fill="var(--color-surface)" stroke="var(--color-border)" />
                        <line x1="180" y1="140" x2="220" y2="140" stroke="var(--color-text)" strokeWidth="2" />
                        <line x1="180" y1="150" x2="210" y2="150" stroke="var(--color-text)" strokeWidth="2" />
                        <line x1="180" y1="160" x2="200" y2="160" stroke="var(--color-text)" strokeWidth="2" />
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
