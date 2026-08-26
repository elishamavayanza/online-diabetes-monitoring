import React from 'react';
import { TreeNode } from "@/react/hook-components/Data/Tree/types";
import { OrganisationIcon, HospitalIcon, DiabetesIcon, NutritionIcon, GeneralMedicineIcon, CardiologyIcon, LaboratoryIcon, MobileUnitIcon, HomeCareIcon } from '../components/OrganisationIcons';
import { CreateOrganisationPayload } from "@/react/features/root/organisations/types";

export async function fetchOrganisations(): Promise<TreeNode[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return [
        {
            id: 'org1',
            label: 'Clinique A',
            icon: React.createElement(OrganisationIcon),
            data: {
                name: 'Clinique A',
                shortName: 'CA',
                type: 'CLINIC',
                email: 'contact@cliniquea.com',
                phone: '+243990000001',
                website: 'https://www.cliniquea.com',
                logoUrl: '',
                active: true,
                address: {
                    street: '12 rue de la Santé',
                    city: 'Goma',
                    postalCode: '00243',
                    country: 'RDC',
                },
            } as CreateOrganisationPayload,
            children: [ /* ... */ ],
        },
        // ...
    ];
}

export async function createOrganisation(payload: CreateOrganisationPayload): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 800));
    console.log('Organisation créée', payload);
    // À remplacer par un vrai appel API
}

export async function updateOrganisation(payload: CreateOrganisationPayload): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 800));
    console.log('Organisation mise à jour', payload);
    // À remplacer par un vrai appel API
}
