import { OrgAdminFormValues } from '../types/orgAdmin';

export async function createOrgAdmin(payload: OrgAdminFormValues): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 800));
    console.log('Administrateur créé', payload);
    // Appel API réel à implémenter
}
