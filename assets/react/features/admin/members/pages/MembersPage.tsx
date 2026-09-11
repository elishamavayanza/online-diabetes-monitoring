import { useState } from 'react';
import { useMembers } from '../hooks/useMembers';
import { MembersTable } from '../components/MembersTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { useI18n } from '@/react/i18n/I18nContext';
import '@/styles/pages/admin/members/_members.scss';

export function MembersPage() {
    const { members, isLoading, error } = useMembers();
    const [isHelpOpen, setIsHelpOpen] = useState(false);
    const { pushAction } = useActionHistory();
    const { t } = useI18n();

    const openHelp = () => {
        setIsHelpOpen(true);
        pushAction(() => setIsHelpOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="members-page">
            <div className="members-page__header">
                <h1>{t('Membres')}</h1>
                <p>{t("Personnes appartenant à l'organisation")}</p>
                <Button variant="secondary" onClick={openHelp}>{t('Aide')}</Button>
            </div>
            <MembersTable members={members} />

            {isHelpOpen && (
                <Modal isOpen={isHelpOpen} onClose={() => setIsHelpOpen(false)}>
                    <p>{t('Cette page liste les membres de votre organisation.')}</p>
                </Modal>
            )}
        </div>
    );
}
