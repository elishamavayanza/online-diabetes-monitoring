// config/physicalActivities.ts
export interface PhysicalActivityOption {
    value: string;
    label: string;
}

export const PHYSICAL_ACTIVITY_OPTIONS: PhysicalActivityOption[] = [
    { value: 'WALKING', label: 'Marche' },
    { value: 'RUNNING', label: 'Course à pied' },
    { value: 'CYCLING', label: 'Vélo' },
    { value: 'SWIMMING', label: 'Natation' },
    { value: 'GYM', label: 'Musculation / Gym' },
    { value: 'YOGA', label: 'Yoga' },
    { value: 'DANCING', label: 'Danse' },
    { value: 'FOOTBALL', label: 'Football' },
    { value: 'BASKETBALL', label: 'Basketball' },
    { value: 'JUMP_ROPE', label: 'Corde à sauter' },
    { value: 'OTHER', label: 'Autre' },
];
