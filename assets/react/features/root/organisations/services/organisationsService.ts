// services/organisationsService.ts
import { TreeNode } from "@/react/hook-components/Data/Tree/types";
import { CreateOrganisationPayload } from '../types';
import apiClient from "@/services/api/client";

interface ApiFeedback<T> {
    status: number;
    error: boolean;
    message: string;
    data: T;
}

interface OrganisationApi {
    id: string;
    name: string;
    shortName?: string;
    type: string;
    email?: string;
    phone?: string;
    website?: string;
    logoUrl?: string;
    active: boolean;
    address?: {
        street?: string;
        city?: string;
        postalCode?: string;
        country?: string;
    };
    // Ajoutez d'autres champs si nécessaire (établissements, etc.)
}

function organisationToTreeNode(org: OrganisationApi): TreeNode {
    return {
        id: org.id,
        label: org.name,
        icon: undefined, // Vous pouvez utiliser une icône par défaut
        data: {
            dataType: 'organisation',
            id: org.id,
            name: org.name,
            shortName: org.shortName || '',
            type: org.type,
            email: org.email || '',
            phone: org.phone || '',
            website: org.website || '',
            logoUrl: org.logoUrl || '',
            active: org.active,
            address: {
                street: org.address?.street || '',
                city: org.address?.city || '',
                postalCode: org.address?.postalCode || '',
                country: org.address?.country || 'RDC',
            },
        } as CreateOrganisationPayload & { id?: string },
        children: [], // Pour l'instant vide ; peut être rempli plus tard
    };
}

/**
 * Récupère la liste des organisations.
 */
export async function fetchOrganisations(): Promise<TreeNode[]> {
    const response = await apiClient.get<ApiFeedback<any>>(  // on utilise any pour flexibilité
        '/healthcare-organizations',
        { params: { limit: 100 } }
    );

    const feedback = response.data;
    if (feedback.error) {
        throw new Error(feedback.message || 'Erreur lors de la récupération des organisations');
    }

    // Extraire le tableau d'organisations selon la structure
    let orgs: OrganisationApi[] = [];

    if (Array.isArray(feedback.data)) {
        // Cas 1 : data est directement un tableau
        orgs = feedback.data;
    } else if (feedback.data && typeof feedback.data === 'object') {
        // Cas 2 : data est un objet, chercher les tableaux possibles
        const possibleArrays = ['items', 'organizations', 'results', 'data'];
        for (const key of possibleArrays) {
            if (Array.isArray(feedback.data[key])) {
                orgs = feedback.data[key];
                break;
            }
        }
        // Si toujours vide, essayer de prendre feedback.data lui-même s'il contient des objets
        if (orgs.length === 0 && feedback.data) {
            // Si feedback.data est un objet avec id et name, c'est peut-être une seule organisation
            if (feedback.data.id && feedback.data.name) {
                orgs = [feedback.data];
            }
        }
    }

    if (!Array.isArray(orgs) || orgs.length === 0) {
        console.error('Structure de réponse inattendue:', response.data);
        throw new Error('Format de réponse inattendu pour la liste des organisations');
    }

    return orgs.map(organisationToTreeNode);
}
/**
 * Crée une organisation.
 */
export async function createOrganisation(
    payload: CreateOrganisationPayload,
    logoFile?: File | null
): Promise<void> {
    const formData = new FormData();
    formData.append('name', payload.name);
    if (payload.shortName) formData.append('shortName', payload.shortName);
    formData.append('type', payload.type);
    if (payload.email) formData.append('email', payload.email);
    if (payload.phone) formData.append('phone', payload.phone);
    if (payload.website) formData.append('website', payload.website);
    formData.append('active', String(payload.active));

    if (payload.address) {
        Object.entries(payload.address).forEach(([key, value]) => {
            if (value) formData.append(`address[${key}]`, value);
        });
    }

    if (logoFile) {
        formData.append('logoFile', logoFile);
    }

    const response = await apiClient.post<ApiFeedback<unknown>>(
        '/healthcare-organizations',
        formData,
        {
            headers: {
                // Ne pas définir Content-Type manuellement ; le navigateur le fera avec la boundary
                'Content-Type': undefined,
            } as any,
        }
    );

    if (response.data.error) {
        throw new Error(response.data.message || "Erreur lors de la création de l'organisation");
    }
}

/**
 * Met à jour une organisation.
 * @param id Identifiant de l'organisation.
 * @param payload Données modifiées.
 * @param logoFile Nouveau fichier logo optionnel.
 */
export async function updateOrganisation(
    id: string,
    payload: CreateOrganisationPayload,
    logoFile?: File | null
): Promise<void> {
    const formData = new FormData();
    formData.append('name', payload.name);
    formData.append('shortName', payload.shortName || '');
    formData.append('type', payload.type);
    formData.append('email', payload.email || '');
    formData.append('phone', payload.phone || '');
    formData.append('website', payload.website || '');
    formData.append('active', String(payload.active));

    if (payload.address) {
        Object.entries(payload.address).forEach(([key, value]) => {
            formData.append(`address[${key}]`, value || '');
        });
    }

    if (logoFile) {
        formData.append('logoFile', logoFile);
    }

    const response = await apiClient.put<ApiFeedback<unknown>>(
        `/healthcare-organizations/${id}`,
        formData,
        {
            headers: {
                'Content-Type': undefined,
            } as any,
        }
    );

    if (response.data.error) {
        throw new Error(response.data.message || "Erreur lors de la mise à jour de l'organisation");
    }
}

/**
 * Suspend une organisation.
 */
export async function suspendOrganisation(id: string): Promise<void> {
    const response = await apiClient.patch<ApiFeedback<unknown>>(
        `/healthcare-organizations/${id}/suspend`
    );
    if (response.data.error) {
        throw new Error(response.data.message || "Erreur lors de la suspension de l'organisation");
    }
}
