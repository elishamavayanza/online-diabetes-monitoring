import { User, UserType } from '../types';

// Mapping entre le filtre et le type réel
const filterToTypeMap: Record<string, UserType | 'Tous'> = {
    'Professionnels': 'Professional',
    'Patients': 'Patient',
    'Administrateurs': 'Administrator',
};

export async function fetchUsers(filter: 'Tous' | 'Professionnels' | 'Patients' | 'Administrateurs'): Promise<User[]> {
    // Simulation d'un délai réseau
    await new Promise((resolve) => setTimeout(resolve, 500));

    // Données simulées
    const allUsers: User[] = [
        {
            id: '1',
            nom: 'Dr. Jean Mukendi',
            email: 'jean.mukendi@diabcare.com',
            type: 'Professional',
            organisation: 'Clinique A',
            statut: 'Active',
            derniereConnexion: '2026-08-24 14:30',
        },
        {
            id: '2',
            nom: 'Marie Zawadi',
            email: 'marie.zawadi@diabcare.com',
            type: 'Patient',
            organisation: null,
            statut: 'Active',
            derniereConnexion: '2026-08-24 10:15',
        },
        {
            id: '3',
            nom: 'Admin Principal',
            email: 'admin@diabcare.com',
            type: 'Administrator',
            organisation: 'Plateforme',
            statut: 'Active',
            derniereConnexion: '2026-08-25 08:00',
        },
        // Ajoutez d'autres utilisateurs si nécessaire
    ];

    if (filter === 'Tous') return allUsers;
    const targetType = filterToTypeMap[filter];
    return allUsers.filter((u) => u.type === targetType);
}
