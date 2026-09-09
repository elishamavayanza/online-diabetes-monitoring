// Dictionnaires de traduction.
//
// Convention : les clés sont les chaînes françaises d'origine du code
// (le français sert de langue source). Le dictionnaire `en` mappe une clé
// française vers son équivalent anglais ; toute clé absente retombe sur la
// chaîne française. `t(key, { param: v })` interpole `{{ param }}`.

import { en as sharedEn } from './extra/shared';

export const STORAGE_KEY = 'diabcare-locale';

export type Locale = 'fr' | 'en';

export const SUPPORTED_LOCALES: Locale[] = ['fr', 'en'];

export function isLocale(value: unknown): value is Locale {
    return typeof value === 'string' && (SUPPORTED_LOCALES as string[]).includes(value);
}

export interface LocaleMeta {
    code: Locale;
    label: string;
}

export const LOCALES: LocaleMeta[] = [
    { code: 'fr', label: 'Français' },
    { code: 'en', label: 'English' },
];

const en: Record<string, string> = {
    // --- Sidebar (tous rôles) ---
    'Tableau de bord': 'Dashboard',
    'Vue générale': 'Overview',
    'Plateforme': 'Platform',
    'Organisations': 'Organizations',
    'Utilisateurs': 'Users',
    'Rôles & permissions': 'Roles & permissions',
    'Système': 'System',
    'Notifications': 'Notifications',
    'Paramètres': 'Settings',
    'Configuration': 'Configuration',
    'Identité & logo': 'Identity & logo',
    'Page d\u2019accueil': 'Homepage',
    'Journaux d\u2019audit': 'Audit logs',
    'Personnel': 'Staff',
    'Professionnels': 'Professionals',
    'Patients': 'Patients',
    'Suivi externe': 'External follow-up',
    'Médicaments': 'Medications',
    'Analytique': 'Analytics',
    'Rapport organisation': 'Organization report',
    'Accueil': 'Home',
    'Mes patients': 'My patients',
    'Rendez-vous': 'Appointments',
    'Agenda': 'Schedule',
    'Communication': 'Communication',
    'Messages': 'Messages',
    'Nutrition': 'Nutrition',
    'Aliments': 'Foods',
    'Ma santé': 'My health',
    'Mesures': 'Measurements',
    'Mon dossier': 'My record',
    'Ma nutrition': 'My nutrition',
    'Traitement': 'Treatment',
    'Mes traitements': 'My treatments',
    'Mes prises': 'My doses',
    'Mes rendez-vous': 'My appointments',
    'Mon équipe': 'My team',
    'Résumé de santé': 'Health summary',

    // --- Chrome layout ---
    'Mon profil': 'My profile',
    'Déconnexion': 'Log out',
    'Ouvrir le menu': 'Open menu',
    'Fermer le panneau droit': 'Close right panel',
    'Ouvrir le panneau droit': 'Open right panel',
    'Retour à la page précédente': 'Back to previous page',
    'Retour': 'Back',
    'Utilisateur': 'User',

    // --- Profile ---
    'Gérez vos informations personnelles': 'Manage your personal information',
    'Aide': 'Help',
    'Vous pouvez modifier votre nom et votre téléphone, puis enregistrer vos changements.': 'You can update your name and phone number, then save your changes.',
    'Nom complet': 'Full name',
    'Téléphone': 'Phone',
    'Photo de profil': 'Profile picture',
    'Cliquez ou déposez une nouvelle photo': 'Click or drop a new picture',
    'PNG, JPG recommandé': 'PNG, JPG recommended',
    'Choisissez une image JPEG, PNG ou WebP.': 'Choose a JPEG, PNG or WebP image.',
    'La photo de profil ne doit pas dépasser 2 Mo.': 'The profile picture must not exceed 2 MB.',
    'Enregistrer les modifications': 'Save changes',
    'Enregistrement...': 'Saving...',
    'Changer le mot de passe': 'Change password',
    'Mise à jour...': 'Updating...',
    'Ancien mot de passe': 'Current password',
    'Nouveau mot de passe': 'New password',
    'Confirmer le nouveau mot de passe': 'Confirm new password',
    'Veuillez remplir tous les champs de mot de passe.': 'Please fill in all password fields.',
    'Les nouveaux mots de passe ne correspondent pas.': 'The new passwords do not match.',
    'Mot de passe mis à jour avec succès.': 'Password updated successfully.',
    'Erreur lors du changement de mot de passe.': 'Error while changing the password.',
    'Profil mis à jour avec succès.': 'Profile updated successfully.',
    'Erreur lors de la sauvegarde.': 'Error while saving.',
    'Impossible de charger le profil.': 'Unable to load the profile.',
    'Profil indisponible': 'Profile unavailable',
    "Langue de l'interface": 'Interface language',

    // --- Login ---
    'Connectez-vous à votre espace': 'Sign in to your workspace',
    'Votre session a expiré. Veuillez vous reconnecter pour continuer.': 'Your session has expired. Please sign in again to continue.',
    'Email ou nom d\'utilisateur': 'Email or username',
    'Mot de passe': 'Password',
    'Se souvenir de moi': 'Remember me',
    'Mot de passe oublié ?': 'Forgot password?',
    'Se connecter': 'Sign in',
    'Connexion en cours...': 'Signing in...',
    'Retour à la connexion': 'Back to sign in',
    'Navigation': 'Navigation',
    'Compte': 'Account',
    'À propos': 'About',

    // --- Settings (ROOT) ---
    'Configuration système': 'System configuration',
    'Gérez les paramètres globaux de la plateforme': 'Manage the platform global settings',
    'Identité': 'Identity',
    'Paramètres indisponibles.': 'Settings unavailable.',
    'Enregistrer': 'Save',
    'Identité de la plateforme': 'Platform identity',
    'Nom du système': 'System name',
    'Logo': 'Logo',
    'Aperçu du logo': 'Logo preview',
    'Cliquez ou déposez un logo ici': 'Click or drop a logo here',
    'PNG, JPEG ou WebP (2 Mo max). Utilisé dans la sidebar, la page de connexion et le pied de page.': 'PNG, JPEG or WebP (2 MB max). Used in the sidebar, login page and footer.',
    'Titre (héro)': 'Title (hero)',
    'Sous-titre (héro)': 'Subtitle (hero)',
    'Titre (À propos)': 'Title (About)',
    'Contenu (À propos)': 'Content (About)',
    'Titre (Fonctionnalités)': 'Title (Features)',
    'Fonctionnalités': 'Features',
    'Titre (Pour qui ?)': 'Title (Who is it for?)',
    'Pour qui ?': 'Who is it for?',
    'Publics cibles': 'Target audiences',
    'Titre (appel à l\u2019action)': 'Title (call to action)',
    'Sous-titre (appel à l\u2019action)': 'Subtitle (call to action)',
    'Slogan du pied de page': 'Footer tagline',
    'Copyright': 'Copyright',
    'Supprimer l\u2019élément': 'Delete item',
    'Supprimer': 'Delete',
    'Ajouter un élément': 'Add an item',
    'Titre': 'Title',
    'Description': 'Description',
    'La configuration système permet au super administrateur de personnaliser l\u2019identité de la plateforme (nom, logo) et les textes affichés sur la page d\u2019accueil publique.': 'System configuration lets the super administrator customize the platform identity (name, logo) and the texts shown on the public homepage.',

    // --- Boilerplate commun (boutons, états, labels génériques) ---
    'Annuler': 'Cancel',
    'Fermer': 'Close',
    'Modifier': 'Edit',
    'Ajouter': 'Add',
    'Créer': 'Create',
    'Recherche': 'Search',
    'Rechercher': 'Search',
    'Valider': 'Confirm',
    'Confirmer': 'Confirm',
    'Oui': 'Yes',
    'Non': 'No',
    'Chargement': 'Loading',
    'Chargement...': 'Loading...',
    'Précédent': 'Previous',
    'Suivant': 'Next',
    'Aucun résultat': 'No results',
    'Aucune donnée': 'No data',
    'Aucun élément': 'No items',
    'Vide': 'Empty',
    'Tous': 'All',
    'Aujourd\u2019hui': 'Today',
    'Hier': 'Yesterday',
    'Date': 'Date',
    'Statut': 'Status',
    'Actions': 'Actions',
    'Nom': 'Name',
    'E-mail': 'Email',
    'Email': 'Email',
    'Adresse': 'Address',
    'Genre': 'Gender',
    'Actif': 'Active',
    'Inactif': 'Inactive',
    'En attente': 'Pending',
    'Terminé': 'Completed',
    'Annulé': 'Cancelled',
    'Gratuit': 'Free',
    'Optionnel': 'Optional',
    'Choisir': 'Choose',
    'Aucun': 'None',
    'Détails': 'Details',
    'Plus de détails': 'More details',
    'Retour à la liste': 'Back to list',
    'Tout sélectionner': 'Select all',
    'Tout désélectionner': 'Deselect all',
    'Avec les éléments sélectionnés': 'With selected items',
    'Onglet {{tab}}': 'Tab {{tab}}',
    'Élément {{index}} sur {{count}}': 'Item {{index}} of {{count}}',
};

interface Dictionary {
    fr: Record<string, string>;
    en: Record<string, string>;
}

const dictionaries: Dictionary = {
    fr: {},
    en: { ...en, ...sharedEn },
};

function interpolate(template: string, params?: Record<string, string | number>): string {
    if (!params) return template;
    return template.replace(/\{\{\s*([\w.]+)\s*\}\}/g, (match, name: string) =>
        params[name] !== undefined ? String(params[name]) : match
    );
}

export function translate(
    key: string,
    locale: Locale,
    params?: Record<string, string | number>
): string {
    const dictionary = dictionaries[locale] ?? dictionaries.fr;
    const value = dictionary[key] ?? key;
    return interpolate(value, params);
}