import { Link, useNavigate } from 'react-router-dom';
import { Card } from '@/react/components/UI/Card';
import { Button } from '@/react/components/UI/Button';
import { usePatientDossierContext } from '../../contexts/PatientDossierContext';
import { getOrCreatePatientConversation } from '../../services/dossierActionsService';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { useI18n } from '@/react/i18n/I18nContext';

export function CommunicationsTab() {
    const { data, basePath } = usePatientDossierContext();
    const { profile } = data;
    const navigate = useNavigate();
    const { showToast } = useToast();
    const { t } = useI18n();

    const handleOpenMessaging = async () => {
        try {
            const conversationId = await getOrCreatePatientConversation(
                profile.id,
                profile.organizationId
            );
            navigate(`${basePath}/messages?conversationId=${conversationId}`);
        } catch (error) {
            console.error(error);
            showToast({
                type: 'error',
                message: t("Impossible d'ouvrir la messagerie avec ce patient."),
            });
        }
    };

    return (
        <div className="patient-dossier-tab patient-dossier-tab--communications">
            <Card>
                <h3>{t('Communication avec le patient')}</h3>
                <p>
                    {t('Contactez {{ name }} via la messagerie interne ou par les coordonnées ci-dessous.', { name: profile.fullName })}
                </p>
                <p><strong>{t('Email :')}</strong> {profile.email || '—'}</p>
                <p><strong>{t('Téléphone :')}</strong> {profile.phone || '—'}</p>
                <div className="patient-dossier-tab__actions">
                    <Button variant="primary" onClick={handleOpenMessaging}>
                        {t('Ouvrir la messagerie')}
                    </Button>
                </div>
            </Card>
        </div>
    );
}
