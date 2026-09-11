import { useState } from 'react';
import { useDepartments } from '../hooks/useDepartments';
import { DepartmentsTable } from '../components/DepartmentsTable';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { Modal } from '@/react/components/UI/Modal';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { useI18n } from '@/react/i18n/I18nContext';
import '@/styles/pages/admin/departments/_departments.scss';

export function DepartmentsPage() {
    const { departments, isLoading, error } = useDepartments();
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const { pushAction } = useActionHistory();
    const { t } = useI18n();

    const openAddModal = () => {
        setIsAddModalOpen(true);
        pushAction(() => setIsAddModalOpen(false));
    };

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    return (
        <div className="departments-page">
            <div className="departments-page__header">
                <h1>{t('Départements')}</h1>
                <Button variant="primary" onClick={openAddModal}>
                    {t('+ Nouveau département')}
                </Button>
            </div>
            <DepartmentsTable departments={departments} />

            {isAddModalOpen && (
                <Modal isOpen={isAddModalOpen} onClose={() => setIsAddModalOpen(false)}>
                    <p>{t("Formulaire d'ajout de département (à implémenter).")}</p>
                </Modal>
            )}
        </div>
    );
}
