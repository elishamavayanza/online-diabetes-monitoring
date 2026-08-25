import { Card } from '@/react/components/UI/Card';
import { Button } from '@/react/components/UI/Button';
import { CareTeamMember } from '../types';

interface TeamMemberCardProps {
    member: CareTeamMember;
}

export function TeamMemberCard({ member }: TeamMemberCardProps) {
    return (
        <Card className="team-member-card">
            <h3>{member.nom}</h3>
            <p><strong>Rôle :</strong> {member.role}</p>
            {member.specialite && <p><strong>Spécialité :</strong> {member.specialite}</p>}
            <p><strong>Fonction :</strong> {member.fonction}</p>
            <div className="team-member-card__actions">
                <Button variant="secondary" size="small" onClick={() => console.log('Voir profil', member.id)}>
                    Voir le profil
                </Button>
                <Button variant="primary" size="small" onClick={() => console.log('Envoyer message', member.id)}>
                    Envoyer un message
                </Button>
            </div>
        </Card>
    );
}
