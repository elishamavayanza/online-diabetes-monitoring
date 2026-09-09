import { useState } from 'react';
import { usePatients } from '../hooks/usePatients';
import { PatientsTable } from '../components/PatientsTable';
import { PatientDetailsDrawer } from '../components/PatientDetailsDrawer';
import { PatientEditModal } from '../components/PatientEditModal';
import { Spinner } from '@/react/components/UI/Spinner';
import { Alert } from '@/react/components/UI/Alert';
import { Button } from '@/react/components/UI/Button';
import { SearchInput } from '@/react/components/Forms/SearchInput';
import { useActionHistory } from '@/react/app/layouts/MainLayout/contexts/ActionHistoryContext';
import '@/styles/pages/admin/patients/_patients.scss';
import { PatientFormModal } from '../components/PatientFormModal';
import { Patient, PatientsFilters } from '../types';
import { PatientFormValues } from "@/react/features/root/users/types/userForm.types";
import { AttachPeopleModal } from '../components/AttachPeopleModal';
import { suspendPatient, reactivatePatient } from '../services/patientsService';
import { SuspensionModal } from '@/react/features/security/components/SuspensionModal';
import { ReactivateModal } from '@/react/features/security/components/ReactivateModal';
import { useToast } from '@/react/app/layouts/MainLayout/contexts/ToastContext';
import { SuspensionPayload } from '@/react/features/security/types';
import { ApiError } from '@/services/api/api.types';

const FilterIcon = () => (
    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2">
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
    </svg>
);

// Convertit un Patient (affichage) en PatientFormValues (formulaire)
function toPatientFormValues(patient: Patient): PatientFormValues {
    return {
        email: patient.email ?? '',
        password: '', // non modifié ici
        fullName: patient.nom,
        phone: patient.telephone ?? '',
        gender: 'UNSPECIFIED',
        locale: 'fr',
        dateOfBirth: patient.dateNaissance,
        placeOfBirth: '',
        bloodType: '',
        heightCm: '',
        avatarUrl: patient.avatarUrl ?? '',
        avatarFile: null,
        address: { street: '', city: '', postalCode: '', country: 'RDC' },
    };
}

export function PatientsPage() {
    const { patients, total, page, limit, isLoading, error, refetch, setFilters, setPage, setSort } = usePatients(); // récupération de refetch
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [search, setSearch] = useState('');
    const [diabeteFilter, setDiabeteFilter] = useState<string>('Tous');
    const [showFilter, setShowFilter] = useState(false);
    const [selectedPatient, setSelectedPatient] = useState<Patient | null>(null);
    const [isDrawerOpen, setIsDrawerOpen] = useState(false);

    // États pour l'édition
    const [editingPatientId, setEditingPatientId] = useState<string | null>(null);
    const [editingPatientData, setEditingPatientData] = useState<PatientFormValues | null>(null);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [isAttachPeopleOpen, setIsAttachPeopleOpen] = useState(false);
    const [selectedPatientId, setSelectedPatientId] = useState<string | null>(null);
    const [attachMode, setAttachMode] = useState<'create' | 'edit'>('create');
    const [suspendingPatient, setSuspendingPatient] = useState<Patient | null>(null);
    const [reactivatingPatient, setReactivatingPatient] = useState<Patient | null>(null);

    const { pushAction } = useActionHistory();
    const { showToast } = useToast();

    const openAddModal = () => {
        setIsAddModalOpen(true);
        pushAction(() => setIsAddModalOpen(false));
    };

    const openDetails = (patient: Patient) => {
        setSelectedPatient(patient);
        setIsDrawerOpen(true);
    };

    const closeDrawer = () => {
        setSelectedPatient(null);
        setIsDrawerOpen(false);
    };

    const handleModify = (patient: Patient) => {
        setEditingPatientId(patient.id);
        setEditingPatientData(toPatientFormValues(patient));
        setIsEditModalOpen(true);
        closeDrawer();
    };

    const handleAttachToPeople = (patient: Patient) => {
        setSelectedPatientId(patient.id);
        const mode = patient.equipeSoins && patient.equipeSoins.trim() !== '' ? 'edit' : 'create';
        setAttachMode(mode);
        setIsAttachPeopleOpen(true);
        closeDrawer();
    };

    const handleSuspend = (patient: Patient) => {
        setSuspendingPatient(patient);
    };

    const handleConfirmSuspend = async (payload: SuspensionPayload) => {
        if (!suspendingPatient) return;
        try {
            await suspendPatient(suspendingPatient.id, payload);
            showToast({
                type: 'success',
                message: `Le patient « ${suspendingPatient.nom} » a été suspendu.`,
            });
            await refetch();
        } catch (err) {
            const message = err instanceof ApiError ? (err.data?.message ?? err.message) : (err instanceof Error ? err.message : 'Une erreur est survenue.');
            showToast({ type: 'error', message });
            throw err;
        }
    };

    const handleReactivate = (patient: Patient) => {
        setReactivatingPatient(patient);
    };

    const handleConfirmReactivate = async () => {
        if (!reactivatingPatient) return;
        try {
            await reactivatePatient(reactivatingPatient.id);
            showToast({
                type: 'success',
                message: `Le patient « ${reactivatingPatient.nom} » a été réactivé.`,
            });
            await refetch();
        } catch (err) {
            const message = err instanceof ApiError ? (err.data?.message ?? err.message) : (err instanceof Error ? err.message : 'Une erreur est survenue.');
            showToast({ type: 'error', message });
            throw err;
        }
    };

    if (isLoading && patients.length === 0) return <Spinner />;
    if (error) return <Alert variant="error">{error}</Alert>;

    const diabeteOptions = ['Tous', 'Type 1', 'Type 2', 'Gestationnel'];

    return (
        <div className="patients-page">
            <div className="patients-page__header">
                <h1>Patients</h1>
                <p>Gérez les patients de votre organisation</p>
            </div>

            <div className="patients-page__actions">
                <div className="patients-page__search">
                    <SearchInput
                        placeholder="Rechercher un patient..."
                        value={search}
                        onSearch={(value: string) => {
                            setSearch(value);
                            setFilters({ search: value, typeDiabete: diabeteFilter as PatientsFilters['typeDiabete'] });
                        }}
                    />
                </div>

                <div className="patients-page__filter-wrapper">
                    <button
                        className={`patients-page__filter-btn ${diabeteFilter !== 'Tous' ? 'patients-page__filter-btn--active' : ''}`}
                        onClick={() => setShowFilter((prev) => !prev)}
                        aria-label="Filtrer par type de diabète"
                        title="Filtrer par type de diabète"
                    >
                        <FilterIcon />
                    </button>

                    {showFilter && (
                        <div className="patients-page__filter-dropdown">
                            {diabeteOptions.map((option) => (
                                <div
                                    key={option}
                                    className={`patients-page__filter-option ${diabeteFilter === option ? 'patients-page__filter-option--selected' : ''}`}
                                    onClick={() => {
                                        setDiabeteFilter(option);
                                        setShowFilter(false);
                                    }}
                                >
                                    {option}
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                <Button variant="primary" onClick={openAddModal} className="patients-page__add-btn">
                    + Ajouter un patient
                </Button>
            </div>

            <PatientsTable
                patients={patients}
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

            {/* Modale de création  onSuccess={refetch} */}
            <PatientFormModal
                isOpen={isAddModalOpen}
                onClose={() => setIsAddModalOpen(false)}
                onSuccess={refetch}
            />

            {/* Modale d'édition  onSuccess={refetch} */}
            {editingPatientId && editingPatientData && (
                <PatientEditModal
                    isOpen={isEditModalOpen}
                    onClose={() => setIsEditModalOpen(false)}
                    patientId={editingPatientId}
                    patientData={editingPatientData}
                    onSuccess={refetch}
                />
            )}

            {/* Drawer de détails */}
            <PatientDetailsDrawer
                patient={selectedPatient}
                isOpen={isDrawerOpen}
                onClose={closeDrawer}
                onModify={handleModify}
                onAttachToPeople={handleAttachToPeople}
                onSuspend={handleSuspend}
                onReactivate={handleReactivate}
            />

            {/* Modale d'attachement  onSuccess={refetch} */}
            {selectedPatientId && (
                <AttachPeopleModal
                    isOpen={isAttachPeopleOpen}
                    onClose={() => setIsAttachPeopleOpen(false)}
                    patientId={selectedPatientId!}
                    mode={attachMode}
                    onSuccess={refetch}
                />
            )}

            <SuspensionModal
                isOpen={!!suspendingPatient}
                onClose={() => setSuspendingPatient(null)}
                title="Suspendre un patient"
                entityLabel={suspendingPatient ? `Patient : ${suspendingPatient.nom}` : ''}
                onConfirm={handleConfirmSuspend}
            />

            <ReactivateModal
                isOpen={!!reactivatingPatient}
                onClose={() => setReactivatingPatient(null)}
                title="Réactiver un patient"
                message={reactivatingPatient ? `Confirmer la réactivation de « ${reactivatingPatient.nom} » ?` : ''}
                onConfirm={handleConfirmReactivate}
            />
        </div>
    );
}
