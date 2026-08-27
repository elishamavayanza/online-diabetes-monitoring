// features/admin/establishments/services/membersService.ts

export type MemberRole = 'CLINICIAN' | 'NUTRITIONIST' | 'PATIENT';

export interface Member {
    id: string;
    nom: string;
    email: string;
    dateNaissance: string;
    avatarUrl?: string;
    role: MemberRole;
}

const mockMembers: Member[] = [
    {
        id: 'm1',
        nom: 'Jean Dupont',
        email: 'jean.dupont@diabcare.com',
        dateNaissance: '1980-05-12',
        avatarUrl: 'https://via.placeholder.com/80',
        role: 'CLINICIAN',
    },
    {
        id: 'm2',
        nom: 'Marie Zawadi',
        email: 'marie.zawadi@diabcare.com',
        dateNaissance: '1995-06-15',
        avatarUrl: 'https://via.placeholder.com/80',
        role: 'NUTRITIONIST',
    },
    {
        id: 'm3',
        nom: 'Paul Kabila',
        email: 'paul.kabila@diabcare.com',
        dateNaissance: '1988-11-03',
        avatarUrl: 'https://via.placeholder.com/80',
        role: 'NUTRITIONIST',
    },
    {
        id: 'm4',
        nom: 'Alice Martin',
        email: 'alice.martin@diabcare.com',
        dateNaissance: '1975-02-28',
        avatarUrl: 'https://via.placeholder.com/80',
        role: 'NUTRITIONIST',
    },
];

/**
 * Récupère les membres d’une entité (établissement ou département).
 * @param entityType 'establishment' | 'department'
 * @param entityId ID de l’entité (ex: 'est-1' ou 'dept-3')
 */
export async function fetchMembersByEntity(
    entityType: 'establishment' | 'department',
    entityId: string
): Promise<Member[]> {
    // Simulation d’un délai réseau
    await new Promise((resolve) => setTimeout(resolve, 500));

    // Ici, on pourrait filtrer selon l’entité réelle.
    // Pour l’exemple, on renvoie toujours la même liste.
    console.log(`Chargement des membres pour ${entityType} ${entityId}`);
    return mockMembers;
}
