import React from 'react';
import {TreeNode} from "@/react/hook-components/Data/Tree/types";

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

export async function fetchOrganisations(): Promise<TreeNode[]> {
    await new Promise((resolve) => setTimeout(resolve, 500));

    return [
        {
            id: 'org1',
            label: 'Clinique A',
            icon: React.createElement(OrganisationIcon),
            children: [
                {
                    id: 'est1',
                    label: 'Bâtiment principal',
                    icon: React.createElement(HospitalIcon),
                    children: [
                        { id: 'dep1', label: 'Diabétologie', icon: React.createElement(DiabetesIcon) },
                        { id: 'dep2', label: 'Nutrition', icon: React.createElement(NutritionIcon) },
                    ],
                },
                // ...
            ],
        },
        // ...
    ];
}
