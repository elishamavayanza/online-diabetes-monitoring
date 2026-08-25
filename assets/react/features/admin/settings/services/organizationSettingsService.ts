import { OrganizationSettings } from '../types';

export async function fetchOrganizationSettings(): Promise<OrganizationSettings> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return {
        nomCourt: 'Hôpital Central',
        type: 'Hospital',
        logoUrl: 'https://via.placeholder.com/100',
        email: 'contact@hopital-central.diabcare.com',
        telephone: '+243 990 000 001',
        siteWeb: 'https://hopital-central.diabcare.com',
        adresse: '12 Avenue de la Paix, Goma',
        statut: 'Active',
    };
}

export async function saveOrganizationSettings(settings: OrganizationSettings): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 500));
    console.log('Paramètres sauvegardés', settings);
}
