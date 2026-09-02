import { Link, useNavigate } from 'react-router-dom';
import { Card } from '@/react/components/UI/Card';
import { Button } from '@/react/components/UI/Button';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { getOrCreatePatientConversation } from '../../services/dossierActionsService';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';

export function CommunicationsTab() {
    const { data } = usePatientDossierContext();
    const { profile } = data;
    const navigate = useNavigate();
    const { showToast } = useToast();

    const handleOpenMessaging = async () => {
        try {
            const conversationId = await getOrCreatePatientConversation(
                profile.id,
                profile.organizationId
            );
            navigate(`/clinician/messages?conversationId=${conversationId}`);
        } catch (error) {
            console.error(error);
            showToast({
                type: 'error',
                message: "Impossible d'ouvrir la messagerie avec ce patient.",
            });
        }
    };

    return (
        <div className="patient-dossier-tab patient-dossier-tab--communications">
            <Card>
                <h3>Communication avec le patient</h3>
                <p>
                    Contactez <strong>{profile.fullName}</strong> via la messagerie interne
                    ou par les coordonnées ci-dessous.
                </p>
                <p><strong>Email :</strong> {profile.email || '—'}</p>
                <p><strong>Téléphone :</strong> {profile.phone || '—'}</p>
                <div className="patient-dossier-tab__actions">
                    <Button variant="primary" onClick={handleOpenMessaging}>
                        Ouvrir la messagerie
                    </Button>
                </div>
            </Card>
        </div>
    );
}
