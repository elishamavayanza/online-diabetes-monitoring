export interface SettingsItem {
    title: string;
    description: string;
}

export interface SettingsData {
    id?: string;
    systemName: string;
    logoUrl: string | null;
    heroTitle: string;
    heroSubtitle: string;
    aboutTitle: string;
    aboutContent: string;
    featuresTitle: string;
    features: SettingsItem[];
    usersTitle: string;
    users: SettingsItem[];
    ctaTitle: string;
    ctaSubtitle: string;
    footerTagline: string;
    footerCopyright: string;
}

export const DEFAULT_SETTINGS: SettingsData = {
    systemName: 'OnlineDIAB',
    logoUrl: null,
    heroTitle: 'Mieux suivre le diabète.\nMieux accompagner chaque patient.',
    heroSubtitle:
        'OnlineDIAB facilite le suivi quotidien des personnes vivant avec le diabète et favorise une meilleure collaboration entre patients et professionnels de santé.',
    aboutTitle: 'Pourquoi OnlineDIAB existe ?',
    aboutContent:
        'Le suivi du diabète nécessite une attention régulière et une bonne coordination entre le patient et les professionnels qui l’accompagnent.\nOnlineDIAB propose un espace centralisé permettant de réunir les informations importantes du suivi médical afin de faciliter l’accompagnement et la prise de décision.',
    featuresTitle: 'Ce que OnlineDIAB permet',
    features: [
        { title: 'Suivi de santé', description: 'Suivre les principaux paramètres de santé et leur évolution au fil du temps.' },
        { title: 'Traitements', description: 'Retrouver les prescriptions et les traitements associés au parcours du patient.' },
        { title: 'Accompagnement médical', description: 'Permettre aux professionnels de santé de mieux suivre leurs patients.' },
        { title: 'Communication', description: 'Faciliter les échanges entre patients et professionnels de santé.' },
        { title: 'Rappels et événements', description: 'Aider à organiser les différents événements liés au suivi médical.' },
    ],
    usersTitle: 'Pour qui ?',
    users: [
        { title: 'Patients', description: 'Un suivi plus clair de leur santé, de leurs traitements et de leur évolution.' },
        { title: 'Professionnels de santé', description: 'Une meilleure visibilité sur les informations nécessaires au suivi de leurs patients.' },
        { title: 'Structures de santé', description: 'Une organisation centralisée des utilisateurs et du suivi médical.' },
    ],
    ctaTitle: 'Un suivi plus simple.\nUne meilleure coordination.',
    ctaSubtitle: 'Découvrez OnlineDIAB et son approche du suivi du diabète.',
    footerTagline:
        'Une plateforme pensée pour faciliter le suivi et l’accompagnement des personnes vivant avec le diabète.',
    footerCopyright: '© 2026 OnlineDIAB — Projet académique et éducatif.',
};