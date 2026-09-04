import { Card } from '@/react/components/UI/Card';
import { Button } from '@/react/components/UI/Button';
import { CareTeamMember } from '../types';
import { useNavigate } from 'react-router-dom'; // ✅ import

interface TeamMemberCardProps {
    member: CareTeamMember;
}

export function TeamMemberCard({ member }: TeamMemberCardProps) {
    const navigate = useNavigate(); // ✅ hook

    return (
        <Card className="team-member-card">
            <div className="team-member-card__content">
                {/* Photo à gauche */}
                <div className="team-member-card__avatar">
                    {member.photoUrl ? (
                        <img src={member.photoUrl} alt={member.nom} />
                    ) : (
                        <div className="team-member-card__avatar-placeholder">
                            {member.nom.charAt(0).toUpperCase()}
                        </div>
                    )}
                </div>

                {/* Informations à droite */}
                <div className="team-member-card__info">
                    <h3>{member.nom}</h3>
                    <p><strong>Rôle :</strong> {member.role}</p>
                    {member.specialite && <p><strong>Spécialité :</strong> {member.specialite}</p>}
                    <p><strong>Fonction :</strong> {member.fonction}</p>
                </div>
            </div>

            <div className="team-member-card__actions">
                <Button
                    variant="primary"
                    size="small"
                    onClick={() => navigate('/patient/messages')}
                >
                    Envoyer un message
                </Button>
            </div>
        </Card>
    );
}
