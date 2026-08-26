import React from 'react';
import { TreeNode } from "@/react/hook-components/Data/Tree/types";
import {
    OrganisationIcon,
    HospitalIcon,
    DiabetesIcon,
    NutritionIcon,
    GeneralMedicineIcon,
    CardiologyIcon,
    LaboratoryIcon,
    MobileUnitIcon,
    HomeCareIcon,
} from '../components/OrganisationIcons';
import { CreateOrganisationPayload } from "@/react/features/root/organisations/types";
import { Establishment } from '../types/establishment';
import { Department } from '../types/department';

export async function fetchOrganisations(): Promise<TreeNode[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return [
        {
            id: 'org1',
            label: 'Clinique A',
            icon: React.createElement(OrganisationIcon),
            data: {
                dataType: 'organisation',
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
            children: [
                {
                    id: 'est1',
                    label: 'Bâtiment principal',
                    icon: React.createElement(HospitalIcon),
                    data: {
                        dataType: 'establishment',
                        id: 'est1',
                        organizationId: 'org1',
                        name: 'Bâtiment principal',
                        phone: '+243990000001',
                        address: {
                            street: '12 rue de la Santé',
                            city: 'Goma',
                            postalCode: '00243',
                            country: 'RDC',
                        },
                    } as Establishment,
                    children: [
                        {
                            id: 'dep1',
                            label: 'Diabétologie',
                            icon: React.createElement(DiabetesIcon),
                            data: {
                                dataType: 'department',
                                id: 'dep1',
                                facilityId: 'est1',
                                name: 'Diabétologie',
                                specialty: 'Endocrinologie',
                            } as Department & { dataType?: string },
                        }
                    ],
                },
                {
                    id: 'est2',
                    label: 'Annexe',
                    icon: React.createElement(HospitalIcon),
                    data: {
                        dataType: 'establishment',
                        id: 'est2',
                        organizationId: 'org1',
                        name: 'Annexe',
                        phone: '+243990000002',
                        address: {
                            street: '5 avenue du Lac',
                            city: 'Goma',
                            postalCode: '00243',
                            country: 'RDC',
                        },
                    } as Establishment,
                    children: [
                        {
                            id: 'dep3',
                            label: 'Diabétologie',
                            icon: React.createElement(DiabetesIcon),
                            data: {
                                dataType: 'department',
                                id: 'dep1',
                                facilityId: 'est1',
                                name: 'Diabétologie',
                                specialty: 'Endocrinologie',
                            } as Department & { dataType?: string },
                        }
                    ],
                },
            ],
        },
        {
            id: 'org2',
            label: 'Hôpital B',
            icon: React.createElement(OrganisationIcon),
            data: {
                name: 'Hôpital B',
                shortName: 'HB',
                type: 'HOSPITAL',
                email: 'contact@hopitalb.com',
                phone: '+243990000003',
                website: 'https://www.hopitalb.com',
                logoUrl: '',
                active: true,
                address: {
                    street: '1 boulevard National',
                    city: 'Kinshasa',
                    postalCode: '00243',
                    country: 'RDC',
                },
            } as CreateOrganisationPayload,
            children: [
                {
                    id: 'est3',
                    label: 'Centre principal',
                    icon: React.createElement(HospitalIcon),
                    data: {
                        id: 'est3',
                        organizationId: 'org2',
                        name: 'Centre principal',
                        phone: '+243990000004',
                        address: {
                            street: '1 boulevard National',
                            city: 'Kinshasa',
                            postalCode: '00243',
                            country: 'RDC',
                        },
                    } as Establishment,
                    children: [
                        { id: 'dep4', label: 'Cardiologie', icon: React.createElement(CardiologyIcon) },
                        { id: 'dep5', label: 'Laboratoire', icon: React.createElement(LaboratoryIcon) },
                    ],
                },
            ],
        },
        {
            id: 'org3',
            label: 'Réseau C',
            icon: React.createElement(OrganisationIcon),
            data: {
                name: 'Réseau C',
                shortName: 'RC',
                type: 'NETWORK',
                email: 'contact@reseauc.com',
                phone: '+243990000005',
                website: 'https://www.reseauc.com',
                logoUrl: '',
                active: false,
                address: {
                    street: '8 avenue des Palmiers',
                    city: 'Lubumbashi',
                    postalCode: '00243',
                    country: 'RDC',
                },
            } as CreateOrganisationPayload,
            children: [
                {
                    id: 'est4',
                    label: 'Unité mobile',
                    icon: React.createElement(MobileUnitIcon),
                    data: {
                        id: 'est4',
                        organizationId: 'org3',
                        name: 'Unité mobile',
                        phone: '+243990000006',
                        address: {
                            street: '8 avenue des Palmiers',
                            city: 'Lubumbashi',
                            postalCode: '00243',
                            country: 'RDC',
                        },
                    } as Establishment,
                    children: [
                        { id: 'dep6', label: 'Suivi à domicile', icon: React.createElement(HomeCareIcon) },
                    ],
                },
            ],
        },
    ];
}

export async function createOrganisation(payload: CreateOrganisationPayload): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 800));
    console.log('Organisation créée', payload);
}

export async function updateOrganisation(payload: CreateOrganisationPayload): Promise<void> {
    await new Promise((resolve) => setTimeout(resolve, 800));
    console.log('Organisation mise à jour', payload);
}
