import { useState } from 'react';
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
import { getProfessionalById } from '../services/professionalsService';


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
    const { professionals, isLoading, error, refetch } = useProfessionals();
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [search, setSearch] = useState('');
    const [selectedProfessional, setSelectedProfessional] = useState<Professional | null>(null);
    const [isDrawerOpen, setIsDrawerOpen] = useState(false);
    const [editingProfessionalId, setEditingProfessionalId] = useState<string | null>(null);
    const [editingProfessionalData, setEditingProfessionalData] = useState<ProfessionalFormValues | null>(null);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [isAttachModalOpen, setIsAttachModalOpen] = useState(false);
    const [attachProfessionalId, setAttachProfessionalId] = useState<string | null>(null);

    const { pushAction } = useActionHistory();

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

    if (isLoading) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    const filteredProfessionals = professionals.filter((pro) => {
        const q = search.toLowerCase();
        return (
            pro.nom.toLowerCase().includes(q) ||
            pro.specialite.toLowerCase().includes(q) ||
            pro.etablissement.toLowerCase().includes(q)
        );
    });

    return (
        <div className="professionals-page">
            <div className="professionals-page__header">
                <h1>Professionnels</h1>
                <p>Gérez les professionnels de votre organisation</p>
            </div>

            <div className="professionals-page__actions">
                <div className="professionals-page__search">
                    <SearchInput
                        placeholder="Rechercher un professionnel..."
                        value={search}
                        onSearch={(value: string) => setSearch(value)}
                    />
                </div>
                <Button variant="primary" onClick={openAddModal}>
                    + Ajouter un professionnel
                </Button>
            </div>

            <ProfessionalsTable
                professionals={filteredProfessionals}
                onViewDetails={openDetails}
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
            />
            {attachProfessionalId && (
                <AttachPatientModal
                    isOpen={isAttachModalOpen}
                    onClose={() => setIsAttachModalOpen(false)}
                    professionalId={attachProfessionalId}
                />
            )}
        </div>
    );
}
