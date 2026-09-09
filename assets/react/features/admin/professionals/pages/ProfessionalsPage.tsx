import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useProfessionals } from '../hooks/useProfessionals';
import { ProfessionalsTable } from '../components/ProfessionalsTable';
import { ProfessionalFormModal } from '../components/ProfessionalFormModal';
import { ProfessionalDetailsDrawer } from '../components/ProfessionalDetailsDrawer';
import { ProfessionalEditModal } from '../components/ProfessionalEditModal';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import { Professional } from '../types/types';
import { ProfessionalFormValues } from "@/react/features/root/users/types/userForm.types";
import { AttachPatientModal } from '../components/AttachPatientModal';
import { getProfessionalById, suspendProfessional, reactivateProfessional } from '../services/professionalsService';
import { SuspensionModal } from '@/react/features/security/components/SuspensionModal';
import { ReactivateModal } from '@/react/features/security/components/ReactivateModal';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { SuspensionPayload } from '@/react/features/security/types';
import { ApiError } from '@/services/api/api.types';
import { useI18n } from '@/react/i18n/I18nContext';


import '@/styles/pages/admin/professionals/_professionals.scss';

// Fonction de conversion d'un professionnel (affichage) vers le type formulaire
function toFormValues(professional: Professional): ProfessionalFormValues {
    return {
        email: professional.email ?? '',
        password: '', // non modifié ici
        fullName: professional.nom,
        phone: '',
        gender: 'UNSPECIFIED',
        locale: 'fr',
        licenseNumber: '', // à remplacer si dispo dans Professional
        professionalType: professional.type === 'Clinician' ? 'CLINICIAN' : 'NUTRITIONIST',
        specialty: professional.specialite,
        signatureUrl: '',
        avatarUrl: professional.avatarUrl ?? '',
        avatarFile: null,
        address: {
            street: '',
            city: '',
            postalCode: '',
            country: 'RDC',
        },
    };
}

export function ProfessionalsPage() {
    // on récupère refetch pour actualiser la liste après action
    const { professionals, total, page, limit, isLoading, error, refetch, setSearch, setPage, setSort } = useProfessionals();
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [search, setSearchInput] = useState('');
    const [selectedProfessional, setSelectedProfessional] = useState<Professional | null>(null);
    const [isDrawerOpen, setIsDrawerOpen] = useState(false);
    const [editingProfessionalId, setEditingProfessionalId] = useState<string | null>(null);
    const [editingProfessionalData, setEditingProfessionalData] = useState<ProfessionalFormValues | null>(null);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [isAttachModalOpen, setIsAttachModalOpen] = useState(false);
    const [attachProfessionalId, setAttachProfessionalId] = useState<string | null>(null);
    const [suspendingProfessional, setSuspendingProfessional] = useState<Professional | null>(null);
    const [reactivatingProfessional, setReactivatingProfessional] = useState<Professional | null>(null);

    const { pushAction } = useActionHistory();
    const navigate = useNavigate();
    const { showToast } = useToast();
    const { t } = useI18n();

    const openAddModal = () => {
        setIsAddModalOpen(true);
        pushAction(() => setIsAddModalOpen(false));
    };

    const openDetails = (professional: Professional) => {
        setSelectedProfessional(professional);
        setIsDrawerOpen(true);
    };

    const closeDrawer = () => {
        setSelectedProfessional(null);
        setIsDrawerOpen(false);
    };

    const handleModify = async (professional: Professional) => {
        try {
            const fullData = await getProfessionalById(professional.id);
            setEditingProfessionalId(professional.id);
            setEditingProfessionalData(fullData);
            setIsEditModalOpen(true);
            closeDrawer();
        } catch (error) {
            console.error('Impossible de charger les données du professionnel', error);
        }
    };

    const handleAttachPatient = (professional: Professional) => {
        setAttachProfessionalId(professional.id);
        setIsAttachModalOpen(true);
        closeDrawer();
    };

    const handleSuspend = (professional: Professional) => {
        setSuspendingProfessional(professional);
    };

    const handleConfirmSuspend = async (payload: SuspensionPayload) => {
        if (!suspendingProfessional) return;
        try {
            await suspendProfessional(suspendingProfessional.id, payload);
            showToast({
                type: 'success',
                message: t('Le professionnel « {{ nom }} » a été suspendu.', { nom: suspendingProfessional.nom }),
            });
            await refetch();
        } catch (err) {
            const message = err instanceof ApiError ? (err.data?.message ?? err.message) : (err instanceof Error ? err.message : 'Une erreur est survenue.');
            showToast({ type: 'error', message });
            throw err;
        }
    };

    const handleReactivate = (professional: Professional) => {
        setReactivatingProfessional(professional);
    };

    const handleConfirmReactivate = async () => {
        if (!reactivatingProfessional) return;
        try {
            await reactivateProfessional(reactivatingProfessional.id);
            showToast({
                type: 'success',
                message: t('Le professionnel « {{ nom }} » a été réactivé.', { nom: reactivatingProfessional.nom }),
            });
            await refetch();
        } catch (err) {
            const message = err instanceof ApiError ? (err.data?.message ?? err.message) : (err instanceof Error ? err.message : 'Une erreur est survenue.');
            showToast({ type: 'error', message });
            throw err;
        }
    };

    if (error) return <Alert variant="error">{error}</Alert>;
    if (isLoading && professionals.length === 0) return <Spinner />;

    return (
        <div className="professionals-page">
            <div className="professionals-page__header">
                <h1>{t('Professionnels')}</h1>
                <p>{t('Gérez les professionnels de votre organisation')}</p>
            </div>

            <div className="professionals-page__actions">
                <div className="professionals-page__search">
                    <SearchInput
                        placeholder={t('Rechercher un professionnel...')}
                        value={search}
                        onSearch={(value: string) => {
                            setSearchInput(value);
                            setSearch(value);
                        }}
                    />
                </div>
                <Button variant="primary" onClick={() => navigate('/admin/professionals/new')}>
                    + {t('Ajouter un professionnel')}
                </Button>
            </div>

            <ProfessionalsTable
                professionals={professionals}
                total={total}
                page={page}
                limit={limit}
                loading={isLoading}
                onPageChange={setPage}
                onSort={setSort}
                onViewDetails={openDetails}
                onSuspend={handleSuspend}
                onReactivate={handleReactivate}
            />

            {/* Modale de création onSuccess={refetch} */}
            <ProfessionalFormModal
                isOpen={isAddModalOpen}
                onClose={() => setIsAddModalOpen(false)}
                onSuccess={refetch}
            />

            {/* Modale d'édition onSuccess={refetch} */}
            {editingProfessionalId && editingProfessionalData && (
                <ProfessionalEditModal
                    isOpen={isEditModalOpen}
                    onClose={() => setIsEditModalOpen(false)}
                    professionalId={editingProfessionalId}
                    professionalData={editingProfessionalData}
                    onSuccess={refetch}
                />
            )}

            {/* Drawer de détails */}
            <ProfessionalDetailsDrawer
                professional={selectedProfessional}
                isOpen={isDrawerOpen}
                onClose={closeDrawer}
                onModify={handleModify}
                onAttachPatient={handleAttachPatient}
                onSuspend={handleSuspend}
                onReactivate={handleReactivate}
            />
            {attachProfessionalId && (
                <AttachPatientModal
                    isOpen={isAttachModalOpen}
                    onClose={() => setIsAttachModalOpen(false)}
                    professionalId={attachProfessionalId}
                    onSuccess={refetch}
                />
            )}

            <SuspensionModal
                isOpen={!!suspendingProfessional}
                onClose={() => setSuspendingProfessional(null)}
                title={t('Suspendre un professionnel')}
                entityLabel={suspendingProfessional ? t('Professionnel : {{ nom }}', { nom: suspendingProfessional.nom }) : ''}
                onConfirm={handleConfirmSuspend}
            />

            <ReactivateModal
                isOpen={!!reactivatingProfessional}
                onClose={() => setReactivatingProfessional(null)}
                title={t('Réactiver un professionnel')}
                message={reactivatingProfessional ? t('Confirmer la réactivation de « {{ nom }} » ?', { nom: reactivatingProfessional.nom }) : ''}
                onConfirm={handleConfirmReactivate}
            />
        </div>
    );
}
