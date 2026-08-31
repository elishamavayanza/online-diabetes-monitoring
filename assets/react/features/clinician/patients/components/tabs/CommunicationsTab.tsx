import { Link } from 'react-router-dom';
import { Card } from '@/react/components/UI/Card';
import { Button } from '@/react/components/UI/Button';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';

export function CommunicationsTab() {
    const { data } = usePatientDossierContext();
    const { profile } = data;

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
                    <Link to="/clinician/messages">
                        <Button variant="primary">Ouvrir la messagerie</Button>
                    </Link>
                </div>
            </Card>
        </div>
    );
}
